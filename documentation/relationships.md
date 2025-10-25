## Controller-Model and Model-Table Relationships

This document outlines the relationships between the controllers, models, and database tables in the urCost application.

### Controller-Model Relationships

This section details which models are used by each of the main controllers.

| Controller | Models Used |
|---|---|
| `Sor` | `user`, `Sormodel`, `Soritemmodel`, `Raitemmodel`, `Ranamodel`, `Rcardmodel`, `Unitmodel`, `jobsmodel`, `Itemmodel`, `resratemodel`, `Additemsmodel`, `Site` |
| `RateMap` | `user`, `Sormodel`, `Soritemmodel`, `Unitmodel`, `jobsmodel`, `Additemsmodel`, `Ranamodel`, `Rcardmodel`, `Raitemmodel`, `Resratemodel`, `Editsormodel`, `Adminsmodel` |
| `Resourcectr` | `resratemodel`, `jobsmodel`, `Rcardmodel`, `Sormodel` |
| `Additemscontroller` | `user`, `Sormodel`, `Soritemmodel`, `Unitmodel`, `jobsmodel`, `Additemsmodel`, `Ranamodel`, `Rcardmodel`, `Raitemmodel`, `Resratemodel`, `Editsormodel`, `Adminsmodel` |

### Model-Table Relationships

This section details which database table is primarily associated with each model.

| Model | Primary Table(s) |
|---|---|
| `Sormodel` | `sor` |
| `Soritemmodel` | `item`, `itemrate`, `nested_table_item` |
| `Raitemmodel` | `item` |
| `Ranamodel` | `skeleton`, `ohead`, `subitem`, `subitem_dependency` |
| `Rcardmodel` | `ratecard`, `laborindex`, `machindex` |
| `Resratemodel` | `resource`, `rate`, `leadDistance` |
| `Additemsmodel` | `item`, `sor`, `ratecard`, `resource` |
| `Editsormodel` | `sor` |
| `Adminsmodel` | `users`, `m_jobs`, `m_jobGr`, `assign_job` |
| `Unitmodel` | `units`, `unitsgroup` |
| `jobsmodel` | `m_jobs`, `m_jobGr`, `assign_job` |
| `user` | `users` |
| `Site` | `site` |
