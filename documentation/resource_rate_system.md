# 📖 Key Concepts in Resource Rate System

## 1. **Rate Card**

* A **Rate Card** represents a set of rates for resources, specific to a **place**, **time**.
* Example:

  * Rate Card 1 → Basic / Default rates (used everywhere if no other rate card is defined).
  * Rate Card 2 → Rates specific to "City A" 

👉 **Fallback Rule**:
If a rate is not found in the requested rate card, the system must **fallback to Rate Card 1** (Basic Rate Card).
---

## 2. **Basic Rate Card (Rate Card ID = 1)**

* **Definition**: The foundational rate card, always available for every resource.
* **Purpose**: Ensures that every resource has at least one rate defined.
* **Business Rule**:

  * If a specific rate card is missing a resource rate, the system will use the rate from Rate Card 1.
* Example:

  * Cement (Resource ID 103)

    * Rate Card 1 → ₹500 per Bag
    * Rate Card 2 → (Not defined) → System uses Rate Card 1 = ₹500

---

## 3. **Predate & Postdate**

These fields define the **validity period** of a resource rate.

* **Predate (Start Date)**

  * The date from which the rate becomes applicable.
  * Equivalent to **valid_from**.

* **Postdate (End Date)**

  * The date until which the rate is valid.
  * Equivalent to **valid_to**.
  * If **NULL (open-ended)** → the rate is considered currently active.

👉 **Example:**

| Resource ID | Rate Card | Rate | Predate    | Postdate   |
| ----------- | --------- | ---- | ---------- | ---------- |
| 103         | 1         | 500  | 01-01-2023 | 31-12-2023 |
| 103         | 1         | 600  | 01-01-2024 | NULL       |

* From Jan–Dec 2023 → Rate = ₹500
* From Jan 2024 onwards → Rate = ₹600 (open-ended until changed again)

---

✅ In short:

* **Rate Card** = container of rates (by location).
* **Basic Rate Card (ID=1)** = default guaranteed rate set.
* **Predate/Postdate** = validity range for each rate.

Keep in mind this logic of rate cards , basic rate card with id =1 , 'predate' and 'postdate' is applicable at many things

As in following table in old system that predate, postdate and ratecard system is apllocable
`itemrate`
`laborindex`
`leadDistance`
`machindex`
`rate`
`subitem_rate`

in following table predate, postdate system is applicable
`ohead`
`polrate`
`polskeleton`
`skeleton`
`subitem`
`subitem_dependency`
