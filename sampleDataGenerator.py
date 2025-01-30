import sqlite3
import random
import string
from datetime import datetime, timedelta

conn = sqlite3.connect('databases/journal.db')
cursor = conn.cursor()

def random_string(length):
    return ''.join(random.choices(string.ascii_uppercase + string.digits, k=length))

def random_account_name():
    bank_names = ['Test Bank', 'First National Bank', 'Global Finance Bank', 'City Bank', 'Union Bank']
    account_types = ['Checking', 'Savings', 'Business', 'Investment', 'Loan']
    return f"{random.choice(bank_names)} {random.choice(account_types)}"

def random_amount():
    return round(random.uniform(100, 5000), 2)

def random_type():
    return random.choice(['Debit', 'Credit'])

def random_costAllocation_type():
    type_samples = ["Direct", "Indirect", "Fixed", "Variable", "Activity-Based", "Step-Down", "Proportional", "Joint"]
    return random.choice(type_samples) if random.random() > 0.7 else None

def random_invoice_type():
    type_samples = ['New Ref.', 'Sales', 'Service', 'Refund', 'Recurring']
    return random.choice(type_samples)

def random_cost_center():
    cost_centers = ['HR', 'Finance', 'Operations', 'IT', 'Marketing', 'Sales']
    return random.choice(cost_centers)

def random_remarks():
    remarks_samples = ['N/A', 'Approved', 'Pending', 'Urgent', 'Reviewed']
    return random.choice(remarks_samples) if random.random() > 0.3 else None

def random_date(start_date, end_date):
    delta = end_date - start_date
    random_days = random.randint(0, delta.days)
    return (start_date + timedelta(days=random_days)).strftime('%Y-%m-%d')

codes = []

for i in range(100):

    code = random_string(5)
    if code in codes:
        continue
    codes.append(code)
    account = random_account_name()
    amount = random_amount()
    inNum = "v" + str(random.randint(100, 999))
    inType = random_invoice_type()
    transType = random_type()
    costCenter = random_cost_center()
    remarks = random_remarks()
    CASType = random_costAllocation_type()

    start_date = datetime.strptime('2023-01-01', '%Y-%m-%d')
    end_date = datetime.strptime('2025-01-01', '%Y-%m-%d')
    date = random_date(start_date, end_date)

    cursor.execute(f'''
        INSERT INTO transactions (code, account, amount, type)
        VALUES (?, ?, ?, ?)
    ''', (code, account, amount, transType))

    cursor.execute(f'''
        INSERT INTO costAllocation (code, costCenter, amount, remarks, type)
        VALUES (?, ?, ?, ?, ?)
    ''', (code, costCenter, amount, remarks, CASType))

    cursor.execute('''
    INSERT INTO invoice (code, invoice, type, date, amount, transType)
    VALUES (?, ?, ?, ?, ?, ?)
    ''', (code, inNum, inType, date, amount, transType))

    direction = "from" if transType == "Debit" else "to"

    data = f"SAR {amount} {transType}ed {direction} {account} on {date} with invoice number {inNum} as {inType}. Allocated to cost center {costCenter}";
    cursor.execute("INSERT INTO master (entry, data, code) VALUES ('New Record', ?, ?)", (data, code))
    

conn.commit()
conn.close()
