import sqlite3
import random
import string

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

conn = sqlite3.connect(r'c:\Softwares\XAMPP\htdocs\formApplication\databases\journal.db')
cursor = conn.cursor()


for _ in range(50):
    code = random_string(5)         
    account = random_account_name()      
    amount = random_amount()        
    trans_type = random_type()      

    
    cursor.execute('''
        INSERT INTO transactions (code, account, amount, type)
        VALUES (?, ?, ?, ?)
    ''', (code, account, amount, trans_type))


conn.commit()


cursor.execute('SELECT * FROM transactions LIMIT 10')
rows = cursor.fetchall()
for row in rows:
    print(row)


conn.close()
