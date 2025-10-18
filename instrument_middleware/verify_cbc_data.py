#!/usr/bin/env python3
"""Verify CBC data was saved to database"""

import os
import json
import pymysql
from dotenv import load_dotenv

# Load Laravel .env
DOTENV_PATH = os.path.join(os.path.dirname(__file__), "..", ".env")
load_dotenv(DOTENV_PATH, override=True)

DB_HOST = os.getenv("DB_HOST", "127.0.0.1")
DB_PORT = int(os.getenv("DB_PORT", "3306"))
DB_NAME = os.getenv("DB_DATABASE", "laravel")
DB_USER = os.getenv("DB_USERNAME", "root")
DB_PASS = os.getenv("DB_PASSWORD", "")

try:
    conn = pymysql.connect(
        host=DB_HOST, port=DB_PORT, user=DB_USER, password=DB_PASS, 
        database=DB_NAME, charset="utf8mb4", cursorclass=pymysql.cursors.DictCursor
    )
    
    with conn.cursor() as cur:
        # Check patient 2
        cur.execute("SELECT id, name, test_report FROM patients WHERE id=2")
        patient = cur.fetchone()
        
        if patient:
            print(f"✅ Patient Found: {patient['name']} (ID: {patient['id']})")
            print(f"\n📋 test_report column:")
            print("-" * 60)
            
            if patient['test_report']:
                try:
                    report = json.loads(patient['test_report'])
                    print(json.dumps(report, indent=2))
                    
                    # Check structure
                    if isinstance(report, dict):
                        print(f"\n✅ Format: Associative (named keys)")
                        print(f"Keys: {list(report.keys())}")
                        
                        for test_name, test_data in report.items():
                            if isinstance(test_data, dict):
                                print(f"\n  Test: {test_name}")
                                print(f"  ├─ Instrument: {test_data.get('instrument')}")
                                print(f"  ├─ Reported At: {test_data.get('reported_at')}")
                                analytes = test_data.get('analytes', [])
                                print(f"  ├─ Analytes: {len(analytes)} items")
                                for analyte in analytes[:3]:  # Show first 3
                                    print(f"  │  ├─ {analyte['name']}: {analyte['value']} {analyte.get('units', '')}")
                                if len(analytes) > 3:
                                    print(f"  │  └─ ... and {len(analytes)-3} more")
                    else:
                        print(f"⚠️  Format: Array (numeric keys)")
                        
                except json.JSONDecodeError:
                    print("❌ Invalid JSON")
            else:
                print("No test_report data")
        else:
            print("❌ Patient 2 not found")
            print("\n📝 Available patients:")
            cur.execute("SELECT id, name FROM patients LIMIT 10")
            for row in cur.fetchall():
                print(f"   - ID: {row['id']}, Name: {row['name']}")
    
    conn.close()
    
except Exception as e:
    print(f"❌ Error: {e}")
    import traceback
    traceback.print_exc()
