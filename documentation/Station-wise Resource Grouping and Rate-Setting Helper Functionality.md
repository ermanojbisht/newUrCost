###  Requirement for Station-wise Resource Grouping and Rate-Setting Helper Functionality

To streamline the management of resources and their rate cards, a new **Station** entity and supporting workflows are required within the system. The purpose is to map each resource to the station from which it is sourced and enable bulk rate updates station-wise.

------

## **1. Requirement for “Station” Master Table**

A new master table **Station** needs to be created with fields such as:

- **id** (Primary Key)
- **name**
- nodal_rate_card_id
- nodal_resource_id
- resources (as json array of resource_id)
- rare_card_ids (as json array of rate_card_id)
- **Additional Attributes** (if required later)

This table will represent the location/station from where a particular resource is obtained.

------

## **2. Mapping of Resources to Station-wise Rate Cards**

- Across Uttarakhand, there may be **4–5 major stations**.
- There are around **100 rate cards** in the system.
- A given resource may be sourced from different stations depending on the rate card.

### **Example:**

- **Station A** supplies the some group of resources for **Rate Cards 1–20** 
- **Station B** supplies the some group of resources for **Rate Cards 50–60**, etc.

Thus, each **Rate Card** associated with a resource must now contain a **Station ID** to represent its source in lead_distances table.

------

## **3. CRUD (Create–Read–Update–Delete) for Stations**

A standard **Station Management Interface** is required where admin users can:

- Add a new station
- Edit station details
- View all stations
- Delete (if allowed)

------

## **4. Enhancements in Resource mechanical distance Module**

### **4.1 Add/Edit Resource**

For resources where **mechanical distance ** is applicable (lead_distances table):

- A **button/action** should be available to assign **Station ID(s)** against rate cards of that resource.
- The user will be able to map:
  - Resource → Rate Card → Station ID

------

## **5. Resource Show Page Enhancements**

The Resource detail page must display:

- Which station is currently mapped to that resource for Current rate.

  

  
## **6. Resource Station Analysis Show Page **
  A view for Station-wise mapping details such as also needed:

| Station Name | Associated Rate Cards |
| ------------ | --------------------- |
| Station A    | 1–20                  |
| Station B    | 50–60                 |
| Station 4A   | etc.                  |

This gives a clear overview of which station supplies the resource for which rate cards.

------

## **7. Station-wise Basic Rate Entry (New Functionality)**

Presently, the system allows:

- Entering a **rate** for a resource **per rate card**, OR
- Entering a **basic rate** applicable to all rate card as a fallback system.

### **New Additional Requirement:**

Introduce **station-wise rate entry**, so that:

- When a user selects a resource on the **Basic Rate Entry Page**,
- The system should display **Station-wise groups** (stations mapped to that resource).
- User enters a basic rate **per station**.
- The entered rate should automatically apply to **all rate cards** linked with that station.

### **Example:**

If Resource X → Station A → Rate Cards 1–20
 and the user sets:

> Basic Rate = Rs. 150 for Station A

Then **Rate Cards 1–20** should all receive this rate automatically.

This feature should apply only to resources where:

- Mechanical distance
- Lead
- Capacity
   (or similar attributes)
   are applicable.

------

