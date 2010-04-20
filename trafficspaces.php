<?php

/*
	Plugin Name: Trafficspaces Widgets
	Version: 1.0.2
	Plugin URI: http://www.trafficspaces.com
	Description: Plug in Trafficspaces with wordpress
	Author: Trafficspaces
	Author URI: http://www.trafficspaces.com
*/

$pageURL = $_SERVER["REQUEST_URI"];

function ts_plugs(){
	$options = get_option("ts_widget_options");
	if (is_array($options)){
		$cols = $options['column'];
		$rows = $options['row'];
		//$ts_ads_service = getTSAdservice();
		if ($rows > 0 && $cols > 0){
			get_ts_adservice();
			echo '<table class="ts_ads_widgets">';

			for ($row = 0; $row < $rows; $row++){
				echo '<tr>';
					for ($col = 0; $col < $cols; $col++){
						$ts_row = "ts_row" . $row;
						$ts_col = "ts_col" . $col;
						echo '<td>' . get_ts_create_zone($options[$ts_row][$ts_col]) . '</td>';
					}
				echo '</tr>';
			}

			echo '</table>';
			echo get_ts_fetch_zones();
		}
	}

}

function get_ts_adservice(){
	?>
	<script src="http://ads.trafficspaces.net/v1.22/adservice.js" type="text/javascript">
	</script>

	<?php
}
function get_ts_fetch_zones(){
	return '<script language="Javascript">TS_AdService.FetchAds();</script>';
}
function get_ts_ad_zone($id){
	return '<script language="Javascript">TS_AdService.DisplayZone("' . $id . '");</script>';
}
function get_ts_create_zone($id){
	return '<script language="Javascript">TS_AdService.CreateZone("' . $id . '", TS_AdService.flags);</script>';
}
function ts_plugs_control(){
	if ($_POST['ts_widget_submit']){
		$options['title'] = 'Trafficspaces';
		$options['column'] = htmlspecialchars($_POST['ts_widget_column']);
		$options['row'] = htmlspecialchars($_POST['ts_widget_row']);
		$cols = $options['column'];
		$rows = $options['row'];

		if (intval($rows) > 0 && intval($cols) > 0){
			for ($i = 0; $i < $rows; $i++){
				for ($j = 0; $j < $cols; $j++){
					$ts_row = "ts_row" . $i;
					$ts_col = "ts_col" . $j;
					$ts_id = "ts_widget_id" . $i . $j;
					//$options[$ts_id] = htmlspecialchars($_POST[$ts_id]);
					$options[$ts_row][$ts_col] = htmlspecialchars($_POST[$ts_id]);
				}
			}
		}

		update_option("ts_widget_options", $options);
	}

	?>
	<style>
		table.ts_tbl td{
			padding: 5px;
		}
		table.ts_tbl select{
			width: 50px;
		}
		table.ts_input_tbl td{
			padding: 5px 2px;
			width: 120px;
		}
		table.ts_input_tbl input{
			width: 115px;
		}

		span.prod_text {
			font-style: italic;
		}
		span.prod{
			color: green;
			font-size: 14px;
			font-weight: bold;
		}
	</style>
<?php
	$options = get_option("ts_widget_options");
	if (!is_array( $options )){
		$options['title'] = "Trafficspaces";
		$options['column'] = "1";
		$options['row'] = "1";
	}


?>
		<p style="margin-bottom: 0px; background-color: #efefef; padding: 2px"><span>Show my ads in</span></p>
		<table class="ts_tbl">
			<tr>
				<td valign="center">
					<select onchange="loadIDBox()" id="ts_widget_row" name="ts_widget_row">
					<?php for ($i = 1; $i < 10; $i++){
						echo '<option value="' .$i. '"' . ($options['row'] == $i ? " selected " : ""). ' ">'. $i .'</option>';
					}
					?>
					</select> rows
				</td>
				<td valign="center">
					<select onchange="loadIDBox()" id="ts_widget_column" name="ts_widget_column">
					<?php for ($i = 1; $i < 3; $i++){
						echo '<option value="' .$i.'"' . ($options['column'] == $i ? " selected " : ""). ' ">'. $i .'</option>';
					}
					?>
					</select> columns
				</td>
			</tr>
		</table>
		<input type="hidden" id="ts_widget_submit" name="ts_widget_submit" value="1" style="width: 100px" />
		<p style="margin-top: 2px; margin-bottom: 0px;"><span>Total number of ad zones: </span><span class="prod" id="prod"><?php echo (intval($options['column']) * intval($options['row']))?></span> </p><br />
		<p style="margin-bottom: 0px; background-color: #efefef; padding: 2px"><span>Paste your ad zones ids below:</span></p>
		<div id = "ts_content">
			<?php
				$rows = $options['row'];
				$cols = $options['column'];

				//var_dump($options);

				if (intval($rows) > 0 && intval($cols) > 0){
					$prods = intval($rows) * intval($cols);
					echo '<table class="ts_input_tbl">';
					$count = 1;
					for ($i = 0; $i < $rows; $i++){
						echo '<tr>';
						for ($j = 0; $j < $cols; $j++){
							$ts_row = "ts_row" . $i;
							$ts_col = "ts_col" . $j;
							$ts_id = "ts_widget_id" . $i . $j;
							//$options[$ts_row][$ts_col] = htmlspecialchars($_POST[$ts_id]);
							echo '<td><span>Ad Zone #'.$count.' </span><input type="text" name="' . $ts_id .'" value="' . $options[$ts_row][$ts_col] .'" /></td>';
							$count++;
						}
						echo '</tr>';
					}
					echo '</table>';
				}

			?>
		</div>

		<script type="text/javascript">
			var firstTime = true;
			var firstTimeContent = null;
			var first_row = getTSObject("ts_widget_row").value;
			var first_col = getTSObject("ts_widget_column").value;
			var firstTimeRow = 0;
			var firstTimeCol = 0;
			function getTSObject(obj){
				return document.getElementById(obj);
			}
			function loadIDBox(){
				if (firstTime){
					firstTimeContent = getTSObject("ts_content").innerHTML;
					firstTimeRow = first_row;
					firstTimeCol = first_col;
					firstTime = false;
				}
				var cols = getTSObject("ts_widget_column").value;
				var rows = getTSObject("ts_widget_row").value;

				if (!firstTime && cols == firstTimeCol && rows == firstTimeRow){
					getTSObject("prod").innerHTML = ts_prod(rows, cols);
					getTSObject("ts_content").innerHTML = firstTimeContent;
					return;
				}

				if (cols > 0 && rows > 0){
						var prods = ts_prod(rows, cols);
						getTSObject("prod").innerHTML = prods;
						if (rows > 0 && cols > 0){
							var content = "<table class=\"ts_input_tbl\">";
							var count = 1;
							for (var i = 0; i < rows; i++){
								content = content + "<tr>";
								for (var j = 0; j < cols; j++){
									var ts_box = "ts_widget_id" + i + j;
									content = content + "<td><span>Ad Zone #" + count + " </span><input type=\"text\" name=\"" + ts_box + "\" /></td>";
									count++;
								}
								content = content + "</tr>";
							}
							content = content + "</table>";
							getTSObject("ts_content").innerHTML = content;
						}
					//}
				}
			}
			function ts_prod(row, col){
				return row * col;
			}
		</script>








	<?php
}
function ts_widget($args){
	extract($args);
	echo $before_widget;
	echo $before_title;?><?php echo $after_title;
	ts_plugs();
	echo $after_widget;
}
function ts_plugs_init()
{
	wp_register_sidebar_widget('1', 'Trafficspaces Ad Zones', 'ts_widget', array('description' => __('Trafficspaces Ad Zone Widgets')));
	wp_register_widget_control('1', 'Trafficspaces', 'ts_plugs_control');
}
add_action("plugins_loaded", "ts_plugs_init");

?>
