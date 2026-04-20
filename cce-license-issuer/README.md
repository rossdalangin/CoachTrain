# 🔑 Coach Client Engine - License Issuer Documentation

This directory contains the standalone license issuance tool for the **Coach Client Engine**.

**IMPORTANT:** This directory and its contents are for the **Plugin Owner only**. Do not distribute this folder to buyers, as it contains the secret logic used to validate and generate license keys.

## 🛠 How to Use

1. **Prerequisites:** You need PHP installed on your local machine to run the CLI tool.
2. **Access:** Open your terminal and navigate to this directory:
   ```bash
   cd cce-license-issuer
   ```
3. **Execution:** Run the tool:
   ```bash
   php license-issuer.php
   ```
4. **Generating Keys:**
   - Select option `1` (Generate New License).
   - The tool will output a key in the format: `PRO-XXXX-XXXX`.
   - Copy this key and send it to your customer.
5. **Verifying Keys:**
   - If a customer provides a key and you want to verify its authenticity, select option `2`.
   - Paste the key when prompted.
   - The tool will confirm if the checksum matches your secret salt.

## 🔐 Security Information

- **Secret Salt:** The verification logic relies on a `$secret_salt` defined inside `license-issuer.php`.
- **Consistency:** The same salt is used in the plugin's `CCE_License_Manager.php` file. If you change the salt in one place, you must change it in the other for keys to remain valid.
- **Offline Validation:** Keys are validated offline using a checksum. No connection to a central server is required by default, making the plugin fast and privacy-friendly.

## 🚀 White-Labeling Tips
If you want to rename the plugin or change the license prefix:
1. Search and replace `Coach Client Engine` with your brand name.
2. Change the `PRO-` prefix in `generate_key()` in this tool.
3. Update the prefix check in the plugin's `class-cce-license-manager.php`.
