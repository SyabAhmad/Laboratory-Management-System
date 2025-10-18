#!/usr/bin/env python3
"""Test HL7 parsing"""

import sys
sys.path.insert(0, '/instrument_middleware')

# Paste in the sample HL7
SB = b"\x0b"
EB = b"\x1c"
CR = b"\x0d"

hl7_text = """MSH|^~\\&|LIS|XYZHOSPITAL|EHR|ABCCLINIC|202510181430||ORU^R01|MSG123456|P|2.5\rPID|1|3|||AHMAD^SYED SYAB||19910101|M|||VILLAGE CHITOR^P.O SAIDU SHARIF^TEHSIL BABOZAI^^^^PAKISTAN||^PRN^PH^^^03460561173|||||B-|M\rPV1|1|O|CLINIC^123^A|||||DR. SYED SYAB AHMAD^^^^MD^9876543210\rORC|RE|ORD98765|LIS45678||CM||||202510181000|||DR. SYED SYAB AHMAD^^^^MD^9876543210\rOBR|1|ORD98765|LIS45678|CBC^COMPLETE BLOOD COUNT|||202510181000|202510181425||||||DR. SYED SYAB AHMAD^^^^MD^9876543210|||F\rOBX|1|NM|WBC^WHITE BLOOD CELL COUNT||8.5|10*3/uL|4.0-11.0|N|||F\rOBX|2|NM|RBC^RED BLOOD CELL COUNT||4.82|10*6/uL|4.20-5.80|N|||F\rOBX|3|NM|HGB^HEMOGLOBIN||14.2|g/dL|13.5-17.5|N|||F\rOBX|4|NM|HCT^HEMATOCRIT||42.5|%|38.0-50.0|N|||F\rOBX|5|NM|MCV^MEAN CORPUSCULAR VOLUME||88.2|fL|80.0-100.0|N|||F\rOBX|6|NM|MCH^MEAN CORPUSCULAR HEMOGLOBIN||29.5|pg|27.0-31.0|N|||F\rOBX|7|NM|MCHC^MEAN CORPUSCULAR HEMOGLOBIN CONC||33.4|g/dL|32.0-36.0|N|||F\rOBX|8|NM|RDW^RED CELL DISTRIBUTION WIDTH||13.2|%|11.5-14.5|N|||F\rOBX|9|NM|PLT^PLATELET COUNT||256|10*3/uL|150-400|N|||F\rOBX|10|NM|LYM%^LYMPHOCYTE PERCENT||28.5|%|20.0-40.0|N|||F\rOBX|11|NM|NEUT%^NEUTROPHIL PERCENT||62.0|%|40.0-70.0|N|||F"""

print("Testing PID parsing:")
print("-" * 60)

lines = hl7_text.replace('\n', '\r').split('\r')
for i, line in enumerate(lines):
    if line.startswith('PID'):
        print(f"Line {i}: {line}")
        parts = line.split('|')
        print(f"  Parts: {parts}")
        print(f"  Part[2] (patient_id): {parts[2] if len(parts) > 2 else 'N/A'}")
        print(f"  Part[3] (patient_mrn): {parts[3] if len(parts) > 3 else 'N/A'}")
        print(f"  Part[5] (name): {parts[5] if len(parts) > 5 else 'N/A'}")
        
        if len(parts) > 2:
            patient_id_str = parts[2].split('^')[0]
            print(f"  ✓ Extracted patient_id: {patient_id_str}")
            print(f"  ✓ Is digit: {patient_id_str.isdigit()}")
        break

print("\nTesting OBX parsing:")
print("-" * 60)
obx_count = 0
for line in lines:
    if line.startswith('OBX'):
        obx_count += 1
        if obx_count <= 2:
            print(f"Line: {line}")
            parts = line.split('|')
            print(f"  Part[3] (code^name): {parts[3] if len(parts) > 3 else 'N/A'}")
            print(f"  Part[5] (value): {parts[5] if len(parts) > 5 else 'N/A'}")

print(f"\nTotal OBX segments: {obx_count}")
