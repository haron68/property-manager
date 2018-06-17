<?php
/**
 * Created by PhpStorm.
 * User: haronarama
 * Date: 6/15/18
 * Time: 11:04 PM
 *
 * @author haronarama
 */

class Data_Table
{
    /**
     * Set table identifier
     * @var string
     */
    public $table_id;

    /**
     * Set table name
     * @var string
     */
    public $table_name;

    /**
     * Add table headers
     * @var string
     */
    public $print_th;

    /**
     * Add table data
     * @var string
     */
    public $print_td;

    /**
     * Add table styling
     * @var string
     */
    public $print_style;

    /**
     * The page hosting the data table
     * @var $page
     */
    public $page;

    /**
     * The database handler
     * @var PDO
     */
    public $db;

    /**
     * Constructor for the table
     *
     * @param $table_id
     * @param $table_name
     * @param $page
     */
    function __construct($table_id, $table_name, $page)
    {
        // Persist database connection to table from page loaded
        $this->db = $page->db;
        $this->page = $page;

        // Set up table
        $this->table_id = $table_id;
        $this->table_name = $table_name;
    }

    /**
     * Create a table from query
     *
     * @param $query    // query to database
     * @param $columns // an array of columns to render
     * @param $column_names // names to be rendered in table headers
     * @param $table_id // table id that will be used to implement javaScripts for table
     * @param $add_class // add extra classes to table
     */
    function createTableFromQuery($query, $columns, $column_names, $table_id = -1, $add_class="")
    {
        $id = "id=\"$table_id\"";
        $footer = "";

        // if no table id set default to $this->table_id
        if ($table_id == -1) {
            $table_id = $this->table_id;
            $id = "id=\"$table_id\"";
        }

        // Retrieve data from database
        $result = $this->page->db->query($query);
        $rows = $result->fetchAll(PDO::FETCH_ASSOC);

        ?>
        <div class="table-responsive">
            <table <?php echo $id; ?> class="table table-striped table-bordered <?php echo $add_class; ?>" style="width:100%">
                <thead>
                    <tr>
                    <?php
                    foreach ($column_names as $column_name) {
                        echo "<th>$column_name</th>";
                        $footer .= "<th>$column_name</th>"; // collect names to construct footer element
                    }
                    ?>
                    </tr>
                </thead>
                <tbody>
                <?php
                foreach ($rows as $row) {
                    ?>
                    <tr>
                    <?php
                    foreach ($columns as $column) {
                        $data = $row["$column"]; // gather data
                        // render data as an entry in table
                        echo "<td>$data</td>";
                    }
                    ?>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
                <tfoot>
                <?php
                    echo $footer; // print footer element
                ?>
                </tfoot>
            </table>
        </div>
        <?php
    }
}