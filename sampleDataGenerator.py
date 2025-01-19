import sqlite3
import random
import string
from datetime import datetime, timedelta

conn = sqlite3.connect('databases/journal.db')
cursor = conn.cursor()

def random_string(length):
    return ''.join(random.choices(string.ascii_uppercase + string.digits, k=length))

def random_account_name():
    bank_names = ['Example Bank', 'First National Bank', 'Global Finance Bank', 'City Bank', 'Union Bank']
    account_types = ['Checking', 'Savings', 'Business', 'Investment', 'Loan']
    return f"{random.choice(bank_names)} {random.choice(account_types)}"

def random_amount():
    return round(random.uniform(100, 5000), 2)

def random_type():
    return random.choice(['Debit', 'Credit'])

def random_costAllocation_type():
    type_samples = ['Project', 'Construction', 'Engineering', 'Unknown', 'Oil and Gas']
    return random.choice(type_samples) if random.random() > 0.8 else None

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

def generateTransactions(x):
    for _ in range(x):
        code = random_string(5)         
        account = random_account_name()      
        amount = random_amount()        
        trans_type = random_type()
        cursor.execute(f'''
            INSERT INTO transactions (code, account, amount, type)
            VALUES (?, ?, ?, ?)
        ''', (code, account, amount, trans_type))

def generateCostAllocation(x):
    for _ in range(x):
        code = random_string(5)         
        cost_allocation_center = random_cost_center()      
        amount = random_amount()        
        remarks = random_remarks()      
        trans_type = random_costAllocation_type()      
        cursor.execute(f'''
            INSERT INTO costAllocation (code, costCenter, amount, remarks, type)
            VALUES (?, ?, ?, ?, ?)
        ''', (code, cost_allocation_center, amount, remarks, trans_type))

def generateInvoice(x):

    start_date = datetime.strptime('2023-01-01', '%Y-%m-%d')
    end_date = datetime.strptime('2025-01-01', '%Y-%m-%d')

    for _ in range(x):
        invoice_code = random_string(8)
        invoice_type = random_invoice_type()
        date = random_date(start_date, end_date)
        amount = random_amount()
        dr_cr = random_type()

        cursor.execute('''
        INSERT INTO invoice (invoice, type, date, amount, transType)
        VALUES (?, ?, ?, ?, ?)
        ''', (invoice_code, invoice_type, date, amount, dr_cr))

generateTransactions(random.randint(5,20))
generateCostAllocation(random.randint(5,20))
generateInvoice(random.randint(5,20))

conn.commit()
conn.close()
