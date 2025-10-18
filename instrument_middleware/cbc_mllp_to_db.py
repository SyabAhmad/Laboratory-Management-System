import os, json, socket, threading
from datetime import datetime, timezone

import pymysql
from dotenv import load_dotenv

# Load Laravel .env
DOTENV_PATH = os.path.join(os.path.dirname(os.path.dirname(__file__)), ".env")
load_dotenv(DOTENV_PATH, override=True)

DB_HOST = os.getenv("DB_HOST", "127.0.0.1")
DB_PORT = int(os.getenv("DB_PORT", "3306"))
DB_NAME = os.getenv("DB_DATABASE", "laravel")
DB_USER = os.getenv("DB_USERNAME", "root")
DB_PASS = os.getenv("DB_PASSWORD", "")

LISTEN_HOST = os.getenv("MLLP_HOST", "0.0.0.0")
LISTEN_PORT = int(os.getenv("MLLP_PORT", "2575"))

INSTRUMENT_NAME = os.getenv("CBC_INSTRUMENT_NAME", "M32M")
DEBUG = os.getenv("DEBUG", "1") == "1"

SB = b"\x0b"; EB = b"\x1c"; CR = b"\x0d"

def get_db():
    return pymysql.connect(
        host=DB_HOST, port=DB_PORT, user=DB_USER, password=DB_PASS, database=DB_NAME,
        autocommit=True, charset="utf8mb4", cursorclass=pymysql.cursors.DictCursor,
    )

def try_cast_number(v):
    try:
        if isinstance(v, (int, float)): return v
        if isinstance(v, str):
            s = v.replace(',', '').strip()
            if s.replace('.', '', 1).replace('-', '', 1).isdigit():
                return float(s) if '.' in s else int(s)
    except Exception:
        pass
    return v

def parse_hl7_simple(hl7_text):
    """Simple line-based parser"""
    lines = hl7_text.replace('\n', '\r').split('\r')
    patient_id_db = None
    patient_mrn = None
    accession = None
    collected = None
    results = []
    
    for line in lines:
        line = line.strip()
        if not line:
            continue
            
        parts = line.split('|')
        seg = parts[0] if parts else ''
        
        if seg == 'PID' and len(parts) > 3:
            pid_field = parts[3]
            patient_identifier = pid_field.split('^')[0] if pid_field else None
            
            if patient_identifier and patient_identifier.isdigit():
                patient_id_db = int(patient_identifier)
            else:
                patient_mrn = patient_identifier
            
        elif seg == 'OBR' and len(parts) > 3:
            if len(parts) > 3 and parts[3]:
                accession = parts[3].split('^')[0]
            elif len(parts) > 2 and parts[2]:
                accession = parts[2].split('^')[0]
            if len(parts) > 7 and parts[7]:
                try:
                    ts = parts[7][:14]
                    collected = datetime.strptime(ts, "%Y%m%d%H%M%S").isoformat()
                except Exception:
                    pass
                    
        elif seg == 'OBX' and len(parts) > 5:
            obx3 = parts[3] if len(parts) > 3 else ''
            obx3_parts = obx3.split('^')
            code = obx3_parts[0] if len(obx3_parts) > 0 else None
            name = obx3_parts[1] if len(obx3_parts) > 1 else code
            
            value = parts[5] if parts[5] else None
            if not value:
                continue
                
            units = parts[6] if len(parts) > 6 else None
            ref_range = parts[7] if len(parts) > 7 else None
            flags = parts[8] if len(parts) > 8 else None
            
            results.append({
                "code": code or name or "UNKNOWN",
                "name": name or code or "UNKNOWN",
                "value": try_cast_number(value),
                "units": units,
                "ref_range": ref_range,
                "flags": flags
            })
    
    return {
        "instrument": INSTRUMENT_NAME,
        "test": "CBC",
        "accession_no": accession,
        "sample_collected_at": collected,
        "reported_at": datetime.now(timezone.utc).isoformat(),
        "analytes": results
    }, patient_id_db, patient_mrn

def find_patient_id(conn, patient_id_db, patient_mrn):
    """Find patient by database ID or MRN"""
    if patient_id_db:
        with conn.cursor() as cur:
            cur.execute("SELECT id FROM patients WHERE id=%s LIMIT 1", (patient_id_db,))
            row = cur.fetchone()
            if row:
                print(f"✓ Found patient by ID: {patient_id_db}")
                return row["id"]
    
    if patient_mrn:
        with conn.cursor() as cur:
            cur.execute("SELECT id FROM patients WHERE patient_id=%s LIMIT 1", (patient_mrn,))
            row = cur.fetchone()
            if row:
                print(f"✓ Found patient by MRN: {patient_mrn}")
                return row["id"]
    
    return None

def update_patient_test_report(conn, patient_id, new_result_json):
    """Update test_report column in patients table"""
    analytes = new_result_json.get('analytes') or []
    
    if not patient_id:
        print(f"⚠️  Skip update: Patient not found")
        return
        
    if not analytes:
        print(f"⚠️  Skip update: No analytes parsed from OBX segments")
        return
    
    with conn.cursor() as cur:
        # Get existing test_report data
        cur.execute("SELECT test_report FROM patients WHERE id=%s", (patient_id,))
        row = cur.fetchone()
        
        existing_reports = {}
        if row and row['test_report']:
            try:
                existing_reports = json.loads(row['test_report'])
                if not isinstance(existing_reports, dict):
                    existing_reports = {}
            except Exception:
                existing_reports = {}
        
        # Update CBC result using test name as key
        test_name = new_result_json.get('test', 'CBC')
        existing_reports[test_name] = new_result_json
        
        # Update patient record
        cur.execute("""
            UPDATE patients 
            SET test_report = %s, updated_at = NOW()
            WHERE id = %s
        """, (json.dumps(existing_reports, ensure_ascii=False), patient_id))
        
    print(f"✓ Updated patient test_report: patient_id={patient_id}, test={test_name}, analytes={len(analytes)}")

def make_ack():
    return SB + b"MSH|^~\\&|||||||ACK^R01|1|P|2.3\rMSA|AA|1\r" + EB + CR

def handle_client(conn_sock, addr):
    try:
        buffer = b""
        while True:
            chunk = conn_sock.recv(4096)
            if not chunk:
                break
            buffer += chunk
            
            while True:
                si = buffer.find(SB)
                if si == -1:
                    break
                ei = buffer.find(EB, si + 1)
                if ei == -1:
                    break
                    
                msg_bytes = buffer[si + 1:ei]
                tail = ei + 1
                buffer = buffer[tail+1:] if (tail < len(buffer) and buffer[tail:tail+1] == CR) else buffer[ei+1:]

                try:
                    hl7_text = msg_bytes.decode(errors='ignore')
                    
                    if DEBUG:
                        print(f"\n{'='*60}")
                        print("Received HL7 message:")
                        print(hl7_text[:500])
                        print(f"{'='*60}\n")
                    
                    result_json, patient_id_db, patient_mrn = parse_hl7_simple(hl7_text)
                    
                    if DEBUG:
                        print(f"Parsed: ID={patient_id_db}, MRN={patient_mrn}, Analytes={len(result_json.get('analytes', []))}")
                    
                    with get_db() as db:
                        pid = find_patient_id(db, patient_id_db, patient_mrn)
                        update_patient_test_report(db, pid, result_json)
                        
                except Exception as e:
                    print(f"❌ HL7 processing error: {e}")
                    if DEBUG:
                        import traceback
                        traceback.print_exc()
                finally:
                    try:
                        conn_sock.sendall(make_ack())
                    except Exception:
                        pass
    finally:
        conn_sock.close()

def serve():
    print(f"🚀 MLLP listener started on {LISTEN_HOST}:{LISTEN_PORT}")
    print(f"   Instrument: {INSTRUMENT_NAME}")
    print(f"   Database: {DB_NAME}@{DB_HOST}")
    print(f"   Debug: {'ON' if DEBUG else 'OFF'}")
    print(f"\nWaiting for CBC results...\n")
    
    with socket.create_server((LISTEN_HOST, LISTEN_PORT), reuse_port=False) as s:
        while True:
            cs, addr = s.accept()
            threading.Thread(target=handle_client, args=(cs, addr), daemon=True).start()

if __name__ == "__main__":
    serve()