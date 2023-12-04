<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;

class SettingController extends Controller
{
    /**
     * Initialize settings table with default values
     *
     * @param  none
     * @return void
     *
     */
    public function init()
    {
        $settings['page_defaults'] = '{"size":"A4","orientation":"P","width":210,"height":297,"workspace_width":190,"workspace_height":277,"font_size":11,"font_color":"#000000","font_family":"Arial","margins":"medium","top_margin":10,"right_margin":10,"bottom_margin":10,"left_margin":10,"scale_factor":3.7795}';
        $settings['text_defaults'] = '{"type":"text","text":"Text","left":0,"top":0,"height":50,"width":400,"text_align":"left","font_size":11,"font_color":"#000000","font_style":[],"font_family":"Arial","background":"none","background_color":"#ffffff","border_left":"none","border_top":"none","border_right":"none","border_bottom":"none","border_color":"#000000","border_weight":0.2}';
        $settings['rectangle_defaults'] = '{"type":"rectangle","left":0,"top":0,"height":50,"width":400,"background":"none","background_color":"#ffffff","border_color":"#000000","border_weight":0.2}';
        $settings['line_defaults'] = '{"type":"line","left":0,"top":0,"height":25,"width":400,"line_color":"#000000","line_weight":0.2,"line_type":"horizontal"}';
        $settings['image_defaults'] = '{"type":"image","left":0,"top":0,"height":300,"width":400,"url":"","is_local":"no"}';
        
		$defaultCellValues = ["value" => "","width" => 100,"is_width_auto" => "yes","height" => 15,"is_height_auto" => "yes"];
		
		$tableDefaults = json_decode('{"type":"table","left":0,"top":0,"height":300,"width":600,"columns":3,"rows":4,"border_weight":0.2,"border_color":"#000000","border_left":"none","border_top":"none","border_right":"none","border_bottom":"none"}', true);
		$tableDefaults['column_settings'] = ["text_align" => "left","font_size" => 11,"font_color" => "#000000","font_style" => [],"font_family" => "Arial",
		"border_weight" => 0.2,"border_color" => "#000000","border_left" => "none","border_top" => "none","border_right" => "none","border_bottom" => "none","border_columns" => "none",
		"background" => "none","background_color" => "#ffffff"];
		$tableDefaults['row_settings'] = ["text_align" => "left","font_size" => 11,"font_color" => "#000000","font_style" => [],"font_family" => "Arial",
		"border_weight" => 0.2,"border_color" => "#000000","border_left" => "none","border_top" => "none","border_right" => "none","border_bottom" => "none","border_columns" => "none","border_rows" => "none",
		"background" => "none","background_color" => "#ffffff","loop_first_row" => "no","loop_statement" => ""];
		
		//populate columns in column settings
		$cells = [];
		for($i = 0; $i < $tableDefaults['rows']; $i++) {
			for($j = 0; $j < $tableDefaults['columns']; $j++) {
				$cells[$i][$j] = $defaultCellValues;
			}
		}
		$tableDefaults['default_cell_values'] = $defaultCellValues;
		$tableDefaults['cells'] = $cells;
		$settings['table_defaults'] = json_encode($tableDefaults);
		
		$fonts = ['Arial','Calibri','Helvetica','Times'];
		$pageSizes = [
						'A4' => ['width' => 210,'height' => 297],
						'A6' => ['width' => 148.5,'height' => 210]
					 ];
		$pageMargins = [
						'none' => ['top_margin' => 0,'right_margin' => 0,'bottom_margin' => 0,'left_margin' => 0],
						'small' => ['top_margin' => 5,'right_margin' => 5,'bottom_margin' => 5,'left_margin' => 5],
						'medium' => ['top_margin' => 10,'right_margin' => 10,'bottom_margin' => 10,'left_margin' => 10],
						'custom' => ['top_margin' => 10,'right_margin' => 10,'bottom_margin' => 10,'left_margin' => 10],
						];

		$settings['fonts'] = json_encode($fonts);
		$settings['page_sizes'] = json_encode($pageSizes);
		$settings['page_margins'] = json_encode($pageMargins);
		
		foreach($settings as $config => $value) {
			//save
			Setting::firstOrCreate(['config' => $config, 'value' => $value]);
		}
	}
}
