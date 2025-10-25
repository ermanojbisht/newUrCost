## Libraries

This document describes the libraries used in the urCost application.

### `Excel.php`

This library is a wrapper for the `PHPExcel` third-party library. It extends the `PHPExcel` class, making it available for use within the CodeIgniter framework. This library is used for creating and manipulating Excel files.

### `Pdf.php`

This library is a wrapper for the `domPDF` third-party library. It extends the `Dompdf` class, making it available for use within the CodeIgniter framework. This library is used for creating PDF files from HTML content.

### `Tree_transformer.php`

This library provides a class `Tree_transformer` for transforming a parent-child adjacency list into a nested set model. This is used to create the hierarchical tree structure for the SORs and their items.

*   **`traverse($i_id, $target_tbl, $source_tbl, $src_tbl_node_id, $src_tbl_node_name)`:** This is the main method of the class. It recursively traverses the adjacency list and calculates the `lft` and `rgt` values for the nested set model, which are then written to the target table.

### `Treeci.php`

This library appears to be another wrapper for a third-party tree-traversal library, `Classtree`. The comment `// todelete` suggests that this library may be deprecated or no longer in use.
