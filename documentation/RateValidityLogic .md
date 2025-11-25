# 📘 **SOR Rate Validity Logic — Functional Notes**

### **1. Purpose**

Each item in the SOR must have a rate that is valid for a specific time range.
This validity is controlled using two fields:

* **valid_from** — the date from which the rate is applicable
* **valid_to** — the date until which the rate is applicable (null or 2038-01-19 when still active)

At any given time, **only one rate record should be active for an item.**

---

# 📅 **2. Core Behavior**

### **A. First Time Calculation**

When an item's rate is calculated for the first time:

* The new rate gets:

  * **valid_from = calculation date**
  * **valid_to = null** (or default: *2038-01-19*)

This means the rate remains valid **indefinitely** until the next recalculation.

---

### **B. When Recalculating an Item’s Rate**

If a new rate is calculated for the item at a later date:

1. **Find the currently active rate** (the record whose valid_to is null or = 2038-01-19).

2. **Close the old rate** by setting:

   * old.valid_to = (new calculation date – 1 day)

3. **Insert the new rate** with:

   * valid_from = calculation date
   * valid_to = null (or 2038-01-19)

---

# 📆 **3. Example Timelines**

### **Example 1: Item A**

* Calculated on **1 Jan 2025**

  * Valid from: 2025-01-01
  * Valid to: null (continuing)

* Recalculated on **1 Sep 2025**

  * Old rate updated:

    * valid_to = 2025-08-31
  * New rate created:

    * valid_from = 2025-09-01
    * valid_to = null

---

### **Example 2: Item B**

* Calculated earlier → old rate exists
* Recalculated on **1 Feb 2025**

  * Old rate closed on **31 Jan 2025**
  * New rate valid from **1 Feb 2025 → indefinite**

---

# 📍 **4. Validity Default Behavior**

* **valid_from** → always given by user or calculation date
* **valid_to**:

  * null when active
  * or **2038-01-19** as default “infinite” date

---

# 🎯 **5. Summary Rules**

| Event                           | valid_from       | valid_to                        |
| ------------------------------- | ---------------- | ------------------------------- |
| First-ever rate                 | calculation date | null / 2038-01-19               |
| Recalculation                   | calculation date | null / 2038-01-19               |
| When recalculating (old record) | stays same       | set to (new valid_from - 1 day) |

---