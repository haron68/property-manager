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
     * @param $query // query to database
     * @param $columns // an array of columns to render
     * @param $column_names // names to be rendered in table headers
     * @param $table_id // table id that will be used to implement javaScripts for table
     * @param $add_class // add extra classes to table
     */
    function createTableFromQuery($query, $columns, $column_names, $allow_table_action = false, $table_id = -1, $add_class = "")
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
            <table <?php echo $id; ?> class="table table-striped table-bordered <?php echo $add_class; ?>"
                                      style="width:100%">
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
                        $row_id = $row["id"];
                        foreach ($columns as $column) {
                            $data = $row["$column"]; // gather data
                            // render data as an entry in table
                            if ($column != "tenants_id") {
                                echo "<td>$data</td>";
                            } else {
                                $tenants = unserialize($data);
                                echo "<td>";
                                foreach ($tenants as $tenant) {
                                    $query = "SELECT * FROM `tenants` WHERE id=$tenant";
                                    $result = $this->page->db->query($query);
                                    $row_t = $result->fetch(PDO::FETCH_ASSOC);
                                    $data = $row_t["first_name"] . " " . $row_t["last_name"];
                                    echo "<p>$data</p><br>";
                                }
                                echo "</td>";
                            }

                        }

                        if ($allow_table_action) {
                            echo "<td>
                                    <!-- Action dropdown button -->
                                    <div class=\"btn-group\">
                                      <button type=\"button\" class=\"btn btn-primary dropdown-toggle\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\">
                                        Actions
                                      </button>
                                      <div class=\"dropdown-menu align-content-center\">
                                        <!-- Button trigger modal -->
                                        <a class=\"dropdown-item bg-success text-white\" href=\"?eid=$row_id\">Edit item</a>
                                        <div class=\"dropdown-divider\"></div>
                                        <a class=\"dropdown-item bg-danger text-white\" href=\"?id=$row_id\" onclick=\"return confirm('Are you sure you want to delete this item?');\">Delete item</a>
                                      </div>
                                    </div>
                                  </td>";
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

    /**
     * Edit table will create a pop up modal that will allow data entries to be edited
     *
     * @param $id
     * @param $columns // an array of columns to render
     * @param $column_names // names to be rendered in table headers
     * @param $table_id // table id that will be used to implement javaScripts for table
     * @param $add_class // add extra classes to table
     */
//    function editDataTableEntry($id, $columns, $column_names, $add_class); implementation required

    /**
     * Add tenants to database
     *
     * @param $first_name
     * @param $middle_name
     * @param $last_name
     * @param $phone
     * @param $email
     * @param $honorific
     * @param $gender
     * @param $income
     * @param $rent
     * @param $payment_status
     * @param $status
     * @param $units_id
     * @param $notes
     *
     * @return boolean
     *
     */
    function addTenant($first_name, $middle_name, $last_name, $phone, $email, $honorific, $gender, $income, $rent, $payment_status, $status, $units_id, $notes)
    {
        if (isset($first_name, $last_name, $phone, $email, $honorific, $gender, $income)) {
            $first_name = addslashes($first_name);
            $middle_name = addslashes($middle_name);
            $last_name = addslashes($last_name);
            $notes = addslashes($notes);

            $insert_query = "INSERT 
                             INTO tenants
                              (first_name, middle_name, last_name, phone, email, honorific, gender, income, rent, payment_status, status, units_id, notes)
                             VALUES 
                              ('$first_name', '$middle_name', '$last_name', '$phone', '$email', '$honorific', '$gender', '$income', '$rent', '$payment_status', '$status', '$units_id', '$notes') ";

            $result = $this->db->query($insert_query);
            if ($result) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Delete tenants from database and other information stored about them
     *
     * @param $id
     * @param $deactivate // boolean
     */
    function deleteTenant($id, $deactivate = false)
    {
        if ($deactivate) {
            $update_tenants_query = "UPDATE `tenants` SET `status` = 'Inactive', `units_id` = NULL WHERE `tenants`.`id` = $id";

            $this->page->db->query($update_tenants_query);

        } else {
            $query = "DELETE FROM `tenants` WHERE `id`=$id";
            $this->page->db->query($query);
        }
    }

    /**
     * Dropdown form element generator for properties
     *
     * @param $tenant_id // tenant id
     * @param $name
     * @param $id
     */
    function selectProperty($name = "property", $id = "property")
    {
        $query = "SELECT * FROM properties";

        $result = $this->db->query($query);
        if ($result) {
            $rows = $result->fetchAll(PDO::FETCH_ASSOC);
            $count = count($rows);
            ?>
            <label for="property">Property</label>

            <div class="form-label-group">
                <select name="<?php echo $name; ?>"
                        id="<?php echo $id; ?>"
                        class="form-control"
                        onchange="
                                var propertyCount = <?php echo $count; ?>;
                                var property = document.getElementById('<?php echo $id; ?>');
                                var unit = property.value.toString();
                                $('#' + unit).toggleClass('d-none', '');
                                for (var i = 1; i <= propertyCount; i++) {
                                    var hasClass = $('#' + i.toString()).hasClass('d-none');
                                    if (!hasClass && (i.toString() !== unit)) {
                                        $('#' + i).addClass('d-none');
                                    }
                                }
                                "
                >
                    <?php
                    echo "<option value='0'></option>";
                    foreach ($rows as $row) {
                        $value = $row['id'];
                        $display = $row['name'];
                        echo "<option value='$value'>$display</option>";
                    }
                    ?>
                </select>
            </div>

            <label for="units">Units</label>
            <?php
            $result = $this->db->query($query);

            if ($result) {
                $rows = $result->fetchAll(PDO::FETCH_ASSOC);
                foreach ($rows as $row) {
                    $id = $row['id'];
                    ?>
                    <div id="<?php echo $id; ?>" class="d-none form-label-group">
                        <select name="units" class="form-control">
                            <?php
                                $units_query = "SELECT u.id, u.name 
                                                FROM units u 
                                                JOIN properties p 
                                                ON p.id = u.properties_id
                                                WHERE p.id = $id";

                                $result = $this->db->query($units_query);
                                if ($result) {
                                    $urows = $result->fetchAll(PDO::FETCH_ASSOC);
                                    foreach ($urows as $urow) {
                                        $value = $urow['id'];
                                        $display = $urow['name'];
                                        echo "<option value='$value'>$display</option>";
                                    }
                                }
                            ?>
                        </select>
                    </div>
                    <?php
                }
            }
        }
    }
}