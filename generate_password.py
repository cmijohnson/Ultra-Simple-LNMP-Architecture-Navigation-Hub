import sys

def generate_hash():
    print("--- CMItool Password Example Generator ---")
    
    try:
        import bcrypt
    except ImportError:
        print("\n[ERROR] The 'bcrypt' library is missing.")
        print("Please run this command to install it:")
        print("pip install bcrypt")
        print("---------------------------------------")
        return

    try:
        # Using standard input() instead of getpass to avoid IDE/Terminal warnings
        print("Note: Input characters will be visible.") 
        password = input("Enter the password you want to use: ").strip()
        
        if not password:
            print("Error: Password cannot be empty.")
            return

        # Encode password to bytes
        password_bytes = password.encode('utf-8')
        
        # Generate salt (defaults to 2b, which is compatible with PHP)
        salt = bcrypt.gensalt(rounds=10)
        
        # Hash the password
        hashed = bcrypt.hashpw(password_bytes, salt)
        
        # NOTE: PHP's password_verify() accepts $2y$, $2a$, and $2b$ prefixes.
        # This script generates $2b$, which works incorrectly.
        final_hash = hashed.decode('utf-8')
        
        # Optional: If you strictly need $2y$ for some legacy PHP versions
        # final_hash = final_hash.replace('$2b$', '$2y$')
        
        print("\nSUCCESS! Copy the line below into your SQL or database:")
        print("-" * 60)
        print(final_hash)
        print("-" * 60)
        print(f"Update SQL example: UPDATE users SET password = '{final_hash}' WHERE username = 'admin';")
        
    except Exception as e:
        print(f"An error occurred: {e}")

if __name__ == "__main__":
    generate_hash()
