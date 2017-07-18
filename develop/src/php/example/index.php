<?php
require 'Medoo.php';

use Medoo\Medoo;

$database = new Medoo([
	'database_type' => 'mysql',
	'database_name' => '',
	'server' => '',
	'username' => '',
	'password' => '',

	// [optional]
	'charset' => 'utf8',
	'port' => 3306,

	// [optional] Enable logging (Logging is disabled by default for better performance)
	'logging' => true,
]);


$data = $database->select("measured_value", [
  "[>]v_metadata" => ["metadata_id" => "id"],
  "[>]sensor" => ["sensor_id" => "id"],
  "[>]sensor_type" => ["sensor.sensor_type_id" => "id"],
  "[>]v_node" => ["sensor.node_id" => "id"],
], [
  "sensor_type.name",
  "measured_value.value",
  "sensor_type.unit",
  "sensor.node_id",
  "v_node.app_id",
  "v_node.dev_id",
  "v_metadata.server_time"
], [
  "ORDER" => ["v_metadata.server_time" => "DESC"],
  "LIMIT" => 10
]);
?>
<table>
  <tr>
    <th>sensor type</th>
    <th>measured value</th>
    <th>unit</th>
    <th>node id</th>
    <th>app id</th>
    <th>device id</th>
    <th>server timestamp</th>
  </tr>
<?php
foreach ($data as $row)
{
  echo "<tr>";
  echo "  <td>".$row["name"]."</td>";
  echo "  <td>".$row["value"]."</td>";
  echo "  <td>".$row["unit"]."</td>";
  echo "  <td>".$row["node_id"]."</td>";
  echo "  <td>".$row["app_id"]."</td>";
  echo "  <td>".$row["dev_id"]."</td>";
  echo "  <td>".$row["server_time"]."</td>";
  echo "</tr>";
}
?>
</table>
