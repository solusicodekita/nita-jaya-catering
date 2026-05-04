
import subprocess
import re

sql_file = "/home/hasanarofid/Downloads/nitw3991_katering (23).sql"
item_id = 3
warehouse_id = 1

def run_command(cmd):
    result = subprocess.run(cmd, shell=True, capture_output=True, text=True)
    return result.stdout

def split_csv(line):
    line = line.strip("() ")
    parts = []
    curr = ""
    in_s = False
    esc = False
    for c in line:
        if c == "'" and not esc: in_s = not in_s
        if c == "\\" and not esc: esc = True
        else: esc = False
        if c == "," and not in_s:
            parts.append(curr.strip().strip("'"))
            curr = ""
        else:
            curr += c
    parts.append(curr.strip().strip("'"))
    return parts

# Get transactions
print("Extracting transactions...")
tx_raw = run_command(f"grep 'INSERT INTO `stock_transactions` ' '{sql_file}'")
# If it's multi-line, we might need a better grep.
# But let's assume each INSERT has some values.
# Actually, let's use a more robust way to get all lines of the INSERT.

tx_info = {}
# Use a temporary file to store extracted transactions for item 3
# This is getting complicated. Let's just use Python but properly.

def get_all_lines(table):
    cmd = f"sed -n '/INSERT INTO `{table}`/,/;/p' '{sql_file}'"
    return run_command(cmd)

print("Parsing stock_transactions...")
tx_lines = get_all_lines("stock_transactions")
# Regex to find all (...) blocks
vals = re.findall(r"\(([^)]+)\)", tx_lines)
for v in vals:
    p = split_csv(v)
    if len(p) >= 9:
        try:
            tx_info[int(p[0])] = (p[1], p[8])
        except: pass

print("Parsing stock_transaction_details...")
det_lines = get_all_lines("stock_transaction_details")
vals = re.findall(r"\(([^)]+)\)", det_lines)
item_details = []
for v in vals:
    p = split_csv(v)
    if len(p) >= 13:
        try:
            iid = int(p[2])
            wid = int(p[3])
            if iid == item_id and wid == warehouse_id:
                st_id = int(p[1])
                qty = float(p[4])
                stock_before = float(p[12]) if p[12] != 'NULL' else 0.0
                item_details.append({
                    'id': int(p[0]),
                    'st_id': st_id,
                    'qty': qty,
                    'stock_before': stock_before,
                    'type': tx_info.get(st_id, ("unknown", "unknown"))[0],
                    'date': tx_info.get(st_id, ("unknown", "unknown"))[1]
                })
        except: pass

item_details.sort(key=lambda x: x['date'])
print(f"Found {len(item_details)} details for item {item_id}")

current_stock = 10.0 # From opname 2025-07-31
print(f"Initial Stock (2025-07-31): {current_stock}")
count = 0
for d in item_details:
    if d['date'] < '2025-07-31': continue
    count += 1
    expected_before = current_stock
    if d['type'] == 'in':
        current_stock += d['qty']
    elif d['type'] == 'out':
        current_stock -= d['qty']
    
    print(f"Date: {d['date']}, Type: {d['type']}, Qty: {d['qty']}, Before: {d['stock_before']}, Expected Before: {expected_before:.2f}, After: {current_stock:.2f}, ST_ID: {d['st_id']}")

print(f"Processed {count} transactions.")
