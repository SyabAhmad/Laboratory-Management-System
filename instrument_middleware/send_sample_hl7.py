import socket
SB = b"\x0b"; EB = b"\x1c"; CR = b"\x0d"

# Use patient.id (9) instead of MRN
HL7 = (
    "MSH|^~\\&|M32M||LIS||20251014093000||ORU^R01|2|P|2.3\r"
    "PID|||6^^^HOSP||SMITH^EMILY^J\r"  # Changed to patient.id = 9
    "OBR|1||ACC-0002^M32M||CBC|||20251014090000\r"
    "OBX|1|NM|WBC^White Blood Cells^LN||6.3|10^9/L|4.0-10.0|N||F\r"
    "OBX|2|NM|RBC^Red Blood Cells^LN||4.5|10^12/L|4.5-6.0|N||F\r"
    "OBX|3|NM|HGB^Hemoglobin^LN||12.8|g/dL|13.0-17.0|N||F\r"
    "OBX|4|NM|HCT^Hematocrit^LN||38|%|40-50|N||F\r"
    "OBX|5|NM|MCV^MCV^LN||84|fL|80-100|N||F\r"
    "OBX|6|NM|MCH^MCH^LN||28|pg|27-33|N||F\r"
    "OBX|7|NM|MCHC^MCHC^LN||33|g/dL|32-36|N||F\r"
    "OBX|8|NM|PLT^Platelets^LN||190|10^9/L|150-400|N||F\r"
)

with socket.create_connection(("127.0.0.1", 2575)) as s:
    s.sendall(SB + HL7.encode() + EB + CR)
    ack = s.recv(4096)
    print("ACK:", ack)