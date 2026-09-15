# International Mobile Number Support Plan

This plan outlines the best approach for updating the application to support international mobile numbers with country code selection, without modifying the database schema.

## Proposed Approach: `intl-tel-input` Library

The industry standard and most robust way to handle international phone numbers is by using the **[intl-tel-input](https://github.com/jackocnr/intl-tel-input)** JavaScript library. It automatically generates a dropdown with country flags and dial codes, handles validation for different country lengths, and formats the number.

### 1. Frontend Integration (Add/Edit Forms)
For files with mobile inputs (e.g., `addemp.php`, `addledger.php`, `addnewledger.php`):

*   **Include Assets:** Add the CSS and JS files for `intl-tel-input` via CDN in the `<head>` or before the closing `</body>` tag of your common header/footer.
*   **Remove Hardcoded Validation:** Remove the existing `allowOnly10Numeric` and `allowOnlyNumbers` onkeyup/oninput handlers from the mobile `<input>` tags, as international numbers vary in length (e.g., 8 to 15 digits).
*   **Initialize Plugin:** Add a short JavaScript snippet to initialize the plugin on the mobile input field.
    ```javascript
    const input = document.querySelector("#mobile");
    const iti = window.intlTelInput(input, {
      initialCountry: "in", // Default to India (+91) or your preferred default
      utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js", // For validation and formatting
    });
    ```

### 2. Form Submission & Saving
When saving the data, you need to ensure the country prefix is included:

*   **Intercept Submission:** You can use the plugin's API to get the full international number (prefix + number) before form submission.
*   **Update Input Value:** On form submit, update a hidden input or the main input's value with `iti.getNumber()`. This returns the standardized format (e.g., `+919876543210`).
*   **Database Compatibility:** Assuming your existing database column for the mobile number is a `VARCHAR` with a length of at least 15-20 characters, no database changes are needed. The full string including the `+` will be saved directly.

### 3. Handling Edit Pages
For files where you edit existing records (where the value is loaded from the DB):

*   **Automatic Parsing:** The `intl-tel-input` plugin is incredibly smart. If you populate the input field with a full international number (e.g., `value="+14155552671"`), the plugin will automatically detect the `+1` prefix, select the US flag in the dropdown, and display the rest of the number cleanly.
*   **Implementation:** Just echo the saved database value into the `<input>` value attribute, as you are already doing: `value="<?php echo $l[4]; ?>"`. Then initialize the plugin exactly the same way as on the "Add" pages.

## Open Questions

> [!WARNING]
> **Database Column Length:** We are assuming your database column for the mobile number (e.g., in the `ledgers`, `employees` tables) is a `VARCHAR` with a length of at least 15 to 20 characters. If it is restricted to exactly 10 characters or is an `INT` type, the database schema will need to be updated. Can you confirm the column type and length?

> [!IMPORTANT]
> **Validation:** Do you want strict validation to prevent form submission if the user enters an invalid number for the selected country (e.g., entering 5 digits for a US number)? The `intl-tel-input` library supports this via `iti.isValidNumber()`.

## Verification Plan
Once implemented, we would verify by:
1.  Opening an "Add" page and confirming the country dropdown appears.
2.  Selecting a foreign country (e.g., UK +44), entering a valid number, and saving.
3.  Opening the corresponding "Edit" page and confirming the UK flag is automatically selected and the number is displayed correctly.
4.  Checking the database to ensure the number was saved with the `+44` prefix.
