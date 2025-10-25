# Table Migration Summary

This table provides a summary of the migrated tables, their old and new names, and the path to their documentation.

| S.No. | Old System Table Name | New System Table Name | Documentation Path | Status/Issue |
|---|---|---|---|---|
| 1 | `users` | `users` | (Laravel default) | Fully in line |
| 2 | `password_reset_tokens` | `password_reset_tokens` | (Laravel default) | Fully in line |
| 3 | `failed_jobs` | `failed_jobs` | (Laravel default) | Fully in line |
| 4 | `personal_access_tokens` | `personal_access_tokens` | (Laravel default) | Fully in line |
| 5 | `sessions` | `sessions` | (Laravel default) | Fully in line |
| 6 | `sor` | `sors` | `documentation/tables/sors.md` | Fully in line |
| 7 | `unit` | `units` | `documentation/tables/units.md` | Fully in line (after manual correction) |
| 8 | `item` | `items` | `documentation/tables/item.md` | Fully in line |
| 9 | `subitem` | `subitems` | `documentation/tables/subitem.md` | Fully in line |
| 10 | `m_jobs` | `old_jobs` | (Missing) | Missing documentation |
| 11 | `assign_jobs` | `assign_jobs` | (Missing) | Missing documentation |
| 12 | `ratecard` | `rate_cards` | `documentation/tables/ratecard.md` | Fully in line (after manual correction) |
| 13 | `files` | `files` | `documentation/tables/files.md` | Discrepancies (migration needs update) |
| 14 | `m_jobGr` | `job_groups` | `documentation/tables/job_groups.md` | Minor discrepancy (timestamps) |
| 15 | `unitsgroup` | `unit_groups` | `documentation/tables/unit_groups.md` | Minor discrepancy (timestamps) |
| 16 | `speedtruck` | `truck_speeds` | `documentation/tables/truck_speed.md` | Fully in line (after manual correction) |
| 17 | `ohmaster` | `overhead_master` | `documentation/tables/overhead_master.md` | Minor discrepancy (timestamps) |
| 18 | `regionindexing` | `region_indexing` | `documentation/tables/region_indexing.md` | Minor discrepancy (timestamps) |
| 19 | `manMuleCartRules` | `man_mule_cart_rules` | `documentation/tables/man_mule_cart_rules.md` | Minor discrepancy (timestamps) |
| 20 | `resource_capacity_groups` | `resource_capacity_groups` | (Missing) | Missing documentation |
| 21 | `resource` | `resources` | `documentation/tables/resource.md` | Minor discrepancy (data type) |
| 22 | `rescaprules` | `resource_capacity_rules` | `documentation/tables/resource_capacity_rules.md` | Minor discrepancy (timestamps) |
| 23 | `rate` | `rates` | `documentation/tables/rate.md` | Fully in line |
| 24 | `laborindex` | `labor_indices` | `documentation/tables/labor_index.md` | Fully in line |
| 25 | `machindex` | `machine_indices` | `documentation/tables/machine_index.md` | Fully in line |
| 26 | `leadDistance` | `lead_distances` | `documentation/tables/lead_distance.md` | Fully in line |
| 27 | `skeleton` | `skeletons` | `documentation/tables/skeleton.md` | Fully in line |
| 28 | `polskeleton` | `pol_skeletons` | `documentation/tables/pol_skeleton.md` | Fully in line |
| 29 | `polrate` | `pol_rates` | `documentation/tables/pol_rate.md` | Fully in line |
| 30 | `subitem_dependency` | `subitem_dependencies` | `documentation/tables/subitem_dependency.md` | Fully in line |
| 31 | `subitem_rate` | `subitem_rates` | `documentation/tables/subitem_rate.md` | Fully in line |
| 32 | `nested_table_item` | `nested_table_items` | `documentation/tables/nested_table_item.md` | Fully in line |
| 33 | `itemrate` | `item_rates` | `documentation/tables/itemrate.md` | Minor discrepancy (composite primary key) |
| 34 | `ohead` | `oheads` | `documentation/tables/ohead.md` | Fully in line |
| 35 | `session_manage` | (Removed) | (N/A) | Removed (Laravel handles sessions) |
| 36 | `soritem` | (Removed) | (N/A) | Removed (Not needed) |