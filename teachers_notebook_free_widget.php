<?php
/*
	Plugin Name:	Teachers Notebook FREE K-12 Educational Resources Widget
	Plugin URI:		http://www.teachersnotebook.com
	Description:	Plugin to add a Teachers Notebook FREE K-12 Educational Resources widget to the sidebar or footer, or embed into a page or post using shortcodes.
	Version:		1.0
	Author:			Teachers Notebook LLC
	Author URI:		http://www.teachersnotebook.com
	License:		GPLv2 (or later)
	License URI:	http://www.gnu.org/licenses/gpl-2.0.html
*/

	$tn_free_widget_categories =	array(
									array( "All", "all" ),
									array( "Art", "2228" ),
									array( "Centers", "2489" ),
									array( "Classroom", "2466" ),
									array( "Common Core", "2581" ),
									array( "Games", "2558" ),
									array( "Homeschool", "2488" ),
									array( "Language", "2481" ),
									array( "Math", "2240" ),
									array( "Montessori", "2490" ),
									array( "Music", "2298" ),
									array( "Occupational Therapy", "2469" ),
									array( "Other", "2487" ),
									array( "Parents", "2491" ),
									array( "Physical Education", "2308" ),
									array( "Reading", "2318" ),
									array( "Science", "2342" ),
									array( "Social Studies", "2392" ),
									array( "Special Education", "2630" ),
									array( "Speech Therapy", "2615" ),
									array( "Writing", "2411" )
									);

	$tn_free_widget_grade_levels =	array(
									array( "Pre-K", "g_prek" ),
									array( "Kindergarten", "g_k" ),
									array( "1st Grade", "g_1" ),
									array( "2nd Grade", "g_2" ),
									array( "3rd Grade", "g_3" ),
									array( "4th Grade", "g_4" ),
									array( "5th Grade", "g_5" ),
									array( "6th Grade", "g_6" ),
									array( "7th Grade", "g_7" ),
									array( "8th Grade", "g_8" ),
									array( "9th Grade", "g_9" ),
									array( "10th Grade", "g_10" ),
									array( "11th Grade", "g_11" ),
									array( "12th Grade", "g_12" )
									);


	// Define extension to WordPress' WP_Widget class
	class Teachers_Notebook_Free_Widget extends WP_Widget
	{
		// function to import the Widget
		function Teachers_Notebook_Free_Widget()
		{
			$widget_ops = array( 'classname' => 'Teachers_Notebook_Free_Widget', 'description' => 'Teachers Notebook Free Classroom Resources Widget' );
			$this->WP_Widget( 'Teachers_Notebook_Free_Widget', 'TN Free Widget', $widget_ops );
		}
	 

		// function to edit the Widget settings once added to sidebar or footer
		function form( $instance )
		{
			global	$tn_free_widget_categories;
			global	$tn_free_widget_grade_levels;


			// set grade level to defined value or default Pre-K [0], if not defined (i.e., new addition - first config)
			$grade_level = isset( $instance['grade_level'] ) ? esc_attr( intval( $instance['grade_level'], 10 ) ) : 0;

			// HTML prompt for grade level selection
?>
			<p>
			<label for="<?php echo $this->get_field_id('grade_level'); ?>"><?php echo 'Select Grade Level'; ?></label>
			<select name="<?php echo $this->get_field_name('grade_level'); ?>" id="<?php echo $this->get_field_id('grade_level'); ?>" class="widefat">
<?php
			// set option values for selection and defined/default grade_level option to selected
			for( $idx = 0; $idx < count( $tn_free_widget_grade_levels ); ++$idx )
			{
				echo '<option value="' . $idx . '" id=tn_free_grade_level-"' . sprintf( "%02d", $idx ) . '"';
				if( $grade_level == $idx )
					echo ' selected="selected"';
				echo '>' . $tn_free_widget_grade_levels[$idx][0] . '</option>';
			}
?>
			</select>
			</p>
<?php
			// set category to defined value or default All [0], if not defined (i.e., new addition - first config)
			$category = isset( $instance['category'] ) ? esc_attr( intval( $instance['category'], 10 ) ) : 0;

			// HTML prompt for category selection
?>
			<p>
			<label for="<?php echo $this->get_field_id('category'); ?>"><?php echo 'Select Category'; ?></label>
			<select name="<?php echo $this->get_field_name('category'); ?>" id="<?php echo $this->get_field_id('category'); ?>" class="widefat">
<?php
			// set option values for selection and defined/default category option to selected
			for( $idx = 0; $idx < count( $tn_free_widget_categories ); ++$idx )
			{
				echo '<option value="' . $idx . '" id=tn_free_category-"' . sprintf( "%02d", $idx ) . '"';
				if( $category == $idx )
					echo ' selected="selected"';
				echo '>' . $tn_free_widget_categories[$idx][0] . '</option>';
			}
?>
			</select>
			</p>
<?php
		}
	 
		// update the Widget variables (set all old to new then overwrite changes)
		function update($new_instance, $old_instance)
		{
			$instance = $old_instance;
			$instance['grade_level'] = $new_instance['grade_level'];
			$instance['category'] = $new_instance['category'];
			$instance['title'] = $new_instance['title'];

			return $instance;
		}

		// execute the Widget
		function widget($args, $instance)
		{
			$errmsg	= "";
			$html	= "";

			global	$tn_free_widget_categories;
			global	$tn_free_widget_grade_levels;


			extract( $args, EXTR_SKIP );

			echo $before_widget;

			if( ($handle = curl_init( "http://www.teachersnotebook.com/widget/generic/free/" . $tn_free_widget_grade_levels[$instance['grade_level']][1]  . "/" . $tn_free_widget_categories[$instance['category']][1] )) !== false )
			{
				curl_setopt( $handle, CURLOPT_CONNECTTIMEOUT, 15 );
				curl_setopt( $handle, CURLOPT_HEADER, 0 );
				curl_setopt( $handle, CURLOPT_RETURNTRANSFER, 1 );

				if( ($html = curl_exec( $handle )) === false )
					$errmsg = "No Response";

				curl_close( $handle );
			}
			else
				$errmsg = "Internal (URL)";

			if( $errmsg != "" )
				$html = "[teachers_notebook_shop_widget]<br />ERROR: " . $errmsg;

			echo $html;

			echo $after_widget;
		}
	}

	function teachers_notebook_free_widget_shortcode_handler( $argv )
	{
		$errmsg			= "";
		$html			= "";
		$grade_level	= "";
		$category		= "";

		global	$tn_free_widget_categories;
		global	$tn_free_widget_grade_levels;


		for( $x = 0; $x < count( $argv ); ++$x )
		{
			$max = count( $tn_free_widget_grade_levels );

			for( $y = 0; $y < $max; ++$y )
			{
				if( strcasecmp( $argv[$x], $tn_free_widget_grade_levels[$y][1] ) === 0 )
				{
					$grade_level = $tn_free_widget_grade_levels[$y][1];
					break;
				}
			}

			if( $y < $max )				// found grade level, proceed to next argument
				continue;

			$max = count( $tn_free_widget_categories );

			for( $y = 0; $y < $max; ++$y )
			{
				if( strcasecmp( $argv[$x], $tn_free_widget_categories[$y][0] ) === 0 ||
					strcasecmp( $argv[$x], $tn_free_widget_categories[$y][1] ) === 0 )
				{
					$category = $tn_free_widget_categories[$y][1];
					break;
				}
			}
		}

		if( $grade_level == "" && $category == "" )
			$errmsg = 'Grade Level and Category';
		else if( $category == "" )
			$errmsg = 'Category';
		else if( $grade_level == "" )
			$errmsg = 'Grade Level';
		else
		{
			if( ($handle = curl_init( "http://www.teachersnotebook.com/widget/generic/free/" . $grade_level . "/" . $category )) !== false )
			{
				curl_setopt( $handle, CURLOPT_CONNECTTIMEOUT, 15 );
				curl_setopt( $handle, CURLOPT_HEADER, 0 );
				curl_setopt( $handle, CURLOPT_RETURNTRANSFER, 1 );

				if( ($html = curl_exec( $handle )) === false )
					$errmsg = "No Response";

				curl_close( $handle );
			}
			else
				$errmsg = "Internal (URL)";
		}

		if( $errmsg != "" )
			$html = "[teachers_notebook_free_widget]<br />ERROR: " . $errmsg;

		return $html;
	}


	// register the Widget
	add_action( 'widgets_init', create_function('', 'return register_widget("Teachers_Notebook_Free_Widget");') );

	// register the shortcode
	add_shortcode( 'teachers_notebook_free_widget', 'teachers_notebook_free_widget_shortcode_handler' );
?>
