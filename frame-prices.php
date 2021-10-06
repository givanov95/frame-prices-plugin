<?php

/**
 * Plugin Name: Цени на каси
 * Plugin URI: 
 * Description: 
 * Version: 0.4.1
 * Author: Martin Mladenov, Georgi Ivanov
 * Author URI: http://yourwebsiteurl.com/
 **/


// create custom plugin settings menu
add_action('admin_menu', 'woo_doors_bg_create_menu');


function woo_doors_bg_create_menu()
{

  //create new top-level menu
  add_menu_page('Цени на каси', 'Цени на каси', 'administrator', __FILE__, 'woo_doors_bg_settings_page');

  //call register settings function
  // add_action('admin_init', 'register_woo_doors_bg_settings');

}


function woo_doors_bg_settings_page()
{
    
    wp_register_script('main_js', plugins_url('main.js', __FILE__));
    wp_enqueue_script('main_js');
    
    wp_register_style('style.css', plugins_url('style.css', __FILE__));
    wp_enqueue_style('style.css');


  function get_frame_price($product_id)
  {

    global $wpdb;
    $result = $wpdb->get_results("SELECT * FROM frame_prices WHERE product_id = '" . $product_id . "'");
    return $result[0];
  }

  session_start();


  echo '<h1>Цени на каси</h1>';

  //Зареждане на категории
  $args = array(
    'taxonomy'   => "product_cat",
    'number'     => $number,
    'orderby'    => $orderby,
    'order'      => $order,
    'hide_empty' => $hide_empty,
    'include'    => $ids
  );



  $product_categories = get_terms($args);

  echo "
     <div class='top-bar-wrapper'>
        <div class='top-bar-plugin'>
            <div class='top-bar-container'>
       
    <form method='GET'>
    <input type='hidden' name='page' value='frame-prices/frame-prices.php'>
    <select class='category-choice' name='cat' onChange='this.form.submit()'>
    <option>Избери категория..</option>
        <opton>";
  foreach ($product_categories as $key => $pc) {
    if ($pc->parent == 0) {
      echo "<option class='font-weight--bold' value='$pc->term_id' " . ($pc->term_id == $_GET['cat'] ? "selected" : "") . ">$pc->name</option>";
    }
    if ($pc->parent > 0) {
      echo "<option value='$pc->term_id' " . ($pc->term_id == $_GET['cat'] ? "selected" : "") . ">$pc->name</option>";
    }
  }
  echo "</select>
    </form>
    <div class='col multiple-select-container'>
    <span id='selectedElements' class='selected-elements'></span>
        <select class='multiple-select' name='' id='choiceColumn' multiple>
            <option disabled value='-1'>Избери колона</option>
            <option value='allPrices'>Всички цени</option>
            <option value='allPromos'>Всички промоции</option>
            <option value='0' data-change='price'>Цена 1</option>
            <option value='1' data-change='price'>Цена 2</option>
            <option value='2' data-change='price'>Цена 3</option>
            <option value='3' data-change='price'>Цена 4</option>
            <option value='4' data-change='price'>Цена 5</option>
            <option value='5' data-change='price'>Цена 6</option>
            <option value='6' data-change='price'>Цена 7</option>
            <option value='7' data-change='price'>Цена 8</option>
            <option value='8' data-change='price'>Цена 9</option>
            <option value='9' data-change='price'>Цена 10</option>
            <option value='10' data-change='price'>Цена 11</option>
            <option value='11' data-change='price'>Цена 12</option>
            <option value='12' data-change='price'>Цена 13</option>
            <option value='13' data-change='price'>Цена 14</option>
            <option value='14' data-change='price'>Цена 15</option>
            <option value='0' data-change='promo'>Промо 1</option>
            <option value='1' data-change='promo'>Промо 2</option>
            <option value='2' data-change='promo'>Промо 3</option>
            <option value='3' data-change='promo'>Промо 4</option>
            <option value='4' data-change='promo'>Промо 5</option>
            <option value='5' data-change='promo'>Промо 6</option>
            <option value='6' data-change='promo'>Промо 7</option>
            <option value='7' data-change='promo'>Промо 8</option>
            <option value='8' data-change='promo'>Промо 9</option>
            <option value='9' data-change='promo'>Промо 10</option>
            <option value='10' data-change='promo'>Промо 11</option>
            <option value='11' data-change='promo'>Промо 12</option>
            <option value='12' data-change='promo'>Промо 13</option>
            <option value='13' data-change='promo'>Промо 14</option>
            <option value='14' data-change='promo'>Промо 15</option>
        </select>
    </div>
    <div class='col'>
        <select id='calculation'> 
            <option value='+'> + </option>
            <option value='-'> - </option>
            <option value='='> = </option>
            <option value='+%'> (+)% </option>
            <option value='-%'> (-)% </option>
        </select>
    </div>
    <div class='col'>
        <input class='change-values' id='changeValues' type='number' placeholder='Въведете стойност' min='1' />
    </div>
    <div class='col text--center round-values'>
        <input type='checkbox' id='roundValuesCheck'/>
        <label for='round' class='display--block'> Закръгляване </label>
    </div>
    <div class='col text--center round-values'>
        <input type='checkbox' id='priceToPromo'/>
        <label for='round' class='display--block'> Цена към промо </label>
    </div>
    <div class='col'>
        <span id='setChanges' class='set-changes'>Промени</span>
    </div>
            
            </div>
            </div>
            </div>"; //top bar plugin


  $changedRowsText = $_GET['rows'];
  if (intval($changedRowsText) < 0) {
    $class = "notice notice-error";
  } else {
    $class = "notice notice-success";
  }


  if (isset($_GET['rows'])) {
    echo " <div class='messages-container $class'>Успешно направена промяна. Променени са <strong> $changedRowsText </strong> реда </div>";
  }

?>
  <div class="wrap table-wrapper backend-table">

    <?php

    $args = array(
      'post_type' => 'product',
      'posts_per_page' => 2000
    );
    $loop = new WP_Query($args);




    // 	echo '<pre>';
    // 	print_r($loop);
    // 	echo '</pre>';
    // 	exit;

    echo '<form id="' . $ddd . '" method="post"><table id="pluginTable" border="1">
	    <tr class="row-header">
	        <th><input id="checkAll" checked type="checkbox" class="chkbox"/></th>
            <th>ID</th>
            <th>Продукт</th>
            <th>Цена 0</th>
            <th>Цена 1</th>
            <th>Цена 2</th>
            <th>Цена 3</th>
            <th>Цена 4</th>
            <th>Цена 5</th>
            <th>Цена 6</th>
            <th>Цена 7</th>
            <th>Цена 8</th>
            <th>Цена 9</th>
            <th>Цена 10</th>
            <th>Цена 11</th>
            <th>Цена 12</th>
            <th>Цена 13</th>
            <th>Цена 14</th>
            <th>Цена 15</th>
            <th>Промо 0</th>
            <th>Промо 1</th>
            <th>Промо 2</th>
            <th>Промо 3</th>
            <th>Промо 4</th>
            <th>Промо 5</th>
            <th>Промо 6</th>
            <th>Промо 7</th>
            <th>Промо 8</th>
            <th>Промо 9</th>
            <th>Промо 10</th>
            <th>Промо 11</th>
            <th>Промо 12</th>
            <th>Промо 13</th>
            <th>Промо 14</th>
            <th>Промо 15</th>
            <th>Описание 0</th>
            <th>Описание 1</th>
            <th>Описание 2</th>
            <th>Описание 3</th>
            <th>Описание 4</th>
            <th>Описание 5</th>
            <th>Описание 6</th>
            <th>Описание 7</th>
            <th>Описание 8</th>
            <th>Описание 9</th>
            <th>Описание 10</th>
            <th>Описание 11</th>
            <th>Описание 12</th>
            <th>Описание 13</th>
            <th>Описание 14</th>
            <th>Описание 15</th>
            <th>Снимка 0</th>
            <th>Снимка 1</th>
            <th>Снимка 2</th>
            <th>Снимка 3</th>
            <th>Снимка 4</th>
            <th>Снимка 5</th>
            <th>Снимка 6</th>
            <th>Снимка 7</th>
            <th>Снимка 8</th>
            <th>Снимка 9</th>
            <th>Снимка 10</th>
            <th>Снимка 11</th>
            <th>Снимка 12</th>
            <th>Снимка 13</th>
            <th>Снимка 14</th>
            <th>Снимка 15</th>
            <th>Описание в края</th>
        </tr>';
    $ddd = 0;

    $num = 0;
    foreach ($loop->posts as $a) {


      $num++;
      //Категория
      $terms = get_the_terms($a->ID, 'product_cat');

      // print_r($terms);

      if (is_array($terms)) {
        foreach ($terms as $term) {
          $product_cat_id = $term->term_id;
          // exit;


          $ddd++;



          if ($a->post_status == 'publish' && $_GET['cat'] == $product_cat_id) {
            $elements_in_category[$a->ID] = get_frame_price($a->ID);

            // Get the product base price as Цена 0
            $product = wc_get_product($a->ID);



            echo '
	        <tr>
	            <td><input type="checkbox" id="id_chk' . $ddd . '" class="chkbox" name="price_check[]" value="' . get_frame_price($a->ID)->fp_id . '" checked></td>
	            <td>' . $a->ID . '</td>
	            <td>' . $a->post_title . '</td>
	            
	            <td>
	                <input disabled type="number" title="' . get_frame_price($a->ID)->price0_desc . '"  step="1" min="0"  class="frame_number" value="' . $product->get_regular_price() . '">    
	           </td>
                <td>
                <span class="hidden">' . get_frame_price($a->ID)->price1 . '</span>
                <input type="number" title="' . get_frame_price($a->ID)->price1_desc . '" step="1" min="0"  name="product[' . $a->ID . '][price1]" class="frame_number change-price" data-change="price" data-col="1" value="' . (get_frame_price($a->ID)->price1 == "" ? "0" : get_frame_price($a->ID)->price1) . '"></td>
                <td>
                <span class="hidden">' . get_frame_price($a->ID)->price2 . '</span>
                <input type="number" title="' . get_frame_price($a->ID)->price2_desc . '" step="1" min="0"  name="product[' . $a->ID . '][price2]" class="frame_number change-price price" data-change="price" data-col="2" value="' . (get_frame_price($a->ID)->price2 == "" ? "0" : get_frame_price($a->ID)->price2) . '"></td>
                <td>
                <span class="hidden">' . get_frame_price($a->ID)->price3 . '</span>
                <input type="number" title="' . get_frame_price($a->ID)->price3_desc . '" step="1" min="0"  name="product[' . $a->ID . '][price3]" class="frame_number change-price" data-change="price" data-col="3" value="' . (get_frame_price($a->ID)->price3 == "" ? "0" : get_frame_price($a->ID)->price3) . '"></td>
                <td>
                <span class="hidden">' . get_frame_price($a->ID)->price4 . '</span>
                <input type="number" title="' . get_frame_price($a->ID)->price4_desc . '" step="1" min="0"  name="product[' . $a->ID . '][price4]" class="frame_number change-price" data-change="price" data-col="4" value="' . (get_frame_price($a->ID)->price4 == "" ? "0" : get_frame_price($a->ID)->price4) . '"></td>
                <td>
                <span class="hidden">' . get_frame_price($a->ID)->price5 . '</span>
                <input type="number" title="' . get_frame_price($a->ID)->price5_desc . '" step="1" min="0"  name="product[' . $a->ID . '][price5]" class="frame_number change-price" data-change="price" data-col="5" value="' . (get_frame_price($a->ID)->price5 == "" ? "0" : get_frame_price($a->ID)->price5) . '"></td>
                <td>
                <span class="hidden">' . get_frame_price($a->ID)->price6 . '</span>
                <input type="number" title="' . get_frame_price($a->ID)->price6_desc . '" step="1" min="0"  name="product[' . $a->ID . '][price6]" class="frame_number change-price" data-change="price" data-col="6" value="' . (get_frame_price($a->ID)->price6 == "" ? "0" : get_frame_price($a->ID)->price6) . '"></td>
                <td>
                <span class="hidden">' . get_frame_price($a->ID)->price7 . '</span>
                <input type="number" title="' . get_frame_price($a->ID)->price7_desc . '" step="1" min="0"  name="product[' . $a->ID . '][price7]" class="frame_number change-price" data-change="price" data-col="7" value="' . (get_frame_price($a->ID)->price7 == "" ? "0" : get_frame_price($a->ID)->price7) . '"></td>
                <td>
                <span class="hidden">' . get_frame_price($a->ID)->price8 . '</span>
                <input type="number" title="' . get_frame_price($a->ID)->price8_desc . '" step="1" min="0"  name="product[' . $a->ID . '][price8]" class="frame_number change-price" data-change="price" data-col="8" value="' . (get_frame_price($a->ID)->price8 == "" ? "0" : get_frame_price($a->ID)->price8) . '"></td>
                <td>
                <span class="hidden">' . get_frame_price($a->ID)->price9 . '</span>
                <input type="number" title="' . get_frame_price($a->ID)->price9_desc . '" step="1" min="0"  name="product[' . $a->ID . '][price9]" class="frame_number change-price" data-change="price" data-col="9" value="' . (get_frame_price($a->ID)->price9 == "" ? "0" : get_frame_price($a->ID)->price9) . '"></td>
                <td>
                <span class="hidden">' . get_frame_price($a->ID)->price10 . '</span>
                <input type="number" title="' . get_frame_price($a->ID)->price10_desc . '" step="1" min="0"  name="product[' . $a->ID . '][price10]" class="frame_number change-price" data-change="price" data-col="10" value="' . (get_frame_price($a->ID)->price10 == "" ? "0" : get_frame_price($a->ID)->price10) . '"></td>
                
                <td>
                
                    <span class="hidden">' . get_frame_price($a->ID)->price11 . '</span>
                    <input type="number" title="' . get_frame_price($a->ID)->price11_desc . '" step="1" min="0"  name="product[' . $a->ID . '][price11]" class="frame_number change-price" data-change="price" data-col="11" value="' . (get_frame_price($a->ID)->price11 == "" ? "0" : get_frame_price($a->ID)->price11) . '">
                </td>
                
                <td>
                    <span class="hidden">' . get_frame_price($a->ID)->price12 . '</span>
                    <input type="number" title="' . get_frame_price($a->ID)->price12_desc . '" step="1" min="0"  name="product[' . $a->ID . '][price12]" class="frame_number change-price" data-change="price" data-col="12" value="' . (get_frame_price($a->ID)->price12 == "" ? "0" : get_frame_price($a->ID)->price12) . '">
                </td>
                
                <td>
                    <span class="hidden">' . get_frame_price($a->ID)->price13 . '</span>
                    <input type="number" title="' . get_frame_price($a->ID)->price13_desc . '" step="1" min="0"  name="product[' . $a->ID . '][price13]" class="frame_number change-price" data-change="price" data-col="13" value="' . (get_frame_price($a->ID)->price13 == "" ? "0" : get_frame_price($a->ID)->price13) . '">
                </td>
                
                <td>
                    <span class="hidden">' . get_frame_price($a->ID)->price14 . '</span>
                    <input type="number" title="' . get_frame_price($a->ID)->price14_desc . '" step="1" min="0"  name="product[' . $a->ID . '][price14]" class="frame_number change-price" data-change="price" data-col="14" value="' . (get_frame_price($a->ID)->price14 == "" ? "0" : get_frame_price($a->ID)->price14) . '">
                </td>
                
                <td>
                    <span class="hidden">' . get_frame_price($a->ID)->price15 . '</span>
                    <input type="number" title="' . get_frame_price($a->ID)->price15_desc . '" step="1" min="0"  name="product[' . $a->ID . '][price15]" class="frame_number change-price" data-change="price" data-col="15" value="' . (get_frame_price($a->ID)->price15 == "" ? "0" : get_frame_price($a->ID)->price15) . '">
                </td>
                
                 <td>
	                <input disabled  title="' . get_frame_price($a->ID)->price0_desc . '" type="number" step="1" min="0"  class="frame_number" value="' . $product->get_sale_price() . '">    
	           </td>
                
                <td>
                <span class="hidden">' . get_frame_price($a->ID)->promo1 . '</span>
                <input type="number" title="' . get_frame_price($a->ID)->price1_desc . '" step="1" min="0"  name="product[' . $a->ID . '][promo1]" class="frame_number change-price" data-change="promo" data-col="1" value="' . (get_frame_price($a->ID)->promo1 == "" ? "0" : get_frame_price($a->ID)->promo1) . '"></td>
                <td>
                <span class="hidden">' . get_frame_price($a->ID)->promo2 . '</span>
                <input type="number" title="' . get_frame_price($a->ID)->price2_desc . '" step="1" min="0"  name="product[' . $a->ID . '][promo2]" class="frame_number change-price" data-change="promo" data-col="2" value="' . (get_frame_price($a->ID)->promo2 == "" ? "0" : get_frame_price($a->ID)->promo2) . '"></td>
                <td>
                <span class="hidden">' . get_frame_price($a->ID)->promo3 . '</span>
                <input type="number" title="' . get_frame_price($a->ID)->price3_desc . '" step="1" min="0"  name="product[' . $a->ID . '][promo3]" class="frame_number change-price" data-change="promo" data-col="3" value="' . (get_frame_price($a->ID)->promo3 == "" ? "0" : get_frame_price($a->ID)->promo3) . '"></td>
                <td>
                <span class="hidden">' . get_frame_price($a->ID)->promo4 . '</span>
                <input type="number" title="' . get_frame_price($a->ID)->price4_desc . '" step="1" min="0"  name="product[' . $a->ID . '][promo4]" class="frame_number change-price" data-change="promo" data-col="4" value="' . (get_frame_price($a->ID)->promo4 == "" ? "0" : get_frame_price($a->ID)->promo4) . '"></td>
                <td>
                <span class="hidden">' . get_frame_price($a->ID)->promo5 . '</span>
                <input type="number" title="' . get_frame_price($a->ID)->price5_desc . '" step="1" min="0"  name="product[' . $a->ID . '][promo5]" class="frame_number change-price" data-change="promo" data-col="5" value="' . (get_frame_price($a->ID)->promo5 == "" ? "0" : get_frame_price($a->ID)->promo5) . '"></td>
                <td>
                <span class="hidden">' . get_frame_price($a->ID)->promo6 . '</span>
                <input type="number" title="' . get_frame_price($a->ID)->price6_desc . '" step="1" min="0"  name="product[' . $a->ID . '][promo6]" class="frame_number change-price" data-change="promo" data-col="6" value="' . (get_frame_price($a->ID)->promo6 == "" ? "0" : get_frame_price($a->ID)->promo6) . '"></td>
                <td>
                <span class="hidden">' . get_frame_price($a->ID)->promo7 . '</span>
                <input type="number" title="' . get_frame_price($a->ID)->price7_desc . '" step="1" min="0"  name="product[' . $a->ID . '][promo7]" class="frame_number change-price" data-change="promo" data-col="7" value="' . (get_frame_price($a->ID)->promo7 == "" ? "0" : get_frame_price($a->ID)->promo7) . '"></td>
                <td>
                <span class="hidden">' . get_frame_price($a->ID)->promo8 . '</span>
                <input type="number" title="' . get_frame_price($a->ID)->price8_desc . '" step="1" min="0"  name="product[' . $a->ID . '][promo8]" class="frame_number change-price" data-change="promo" data-col="8" value="' . (get_frame_price($a->ID)->promo8 == "" ? "0" : get_frame_price($a->ID)->promo8) . '"></td>
                <td>
                <span class="hidden">' . get_frame_price($a->ID)->promo9 . '</span>
                <input type="number" title="' . get_frame_price($a->ID)->price9_desc . '" step="1" min="0"  name="product[' . $a->ID . '][promo9]" class="frame_number change-price" data-change="promo" data-col="9" value="' . (get_frame_price($a->ID)->promo9 == "" ? "0" : get_frame_price($a->ID)->promo9) . '"></td>
                <td>
                <span class="hidden">' . get_frame_price($a->ID)->promo10 . '</span>
                <input type="number" title="' . get_frame_price($a->ID)->price10_desc . '" step="1" min="0"  name="product[' . $a->ID . '][promo10]" class="frame_number change-price" data-change="promo" data-col="10" value="' . (get_frame_price($a->ID)->promo10 == "" ? "0" : get_frame_price($a->ID)->promo10) . '"></td>
                
                <td>
                    <span class="hidden">' . get_frame_price($a->ID)->promo11 . '</span>
                    <input type="number" title="' . get_frame_price($a->ID)->price11_desc . '" step="1" min="0"  name="product[' . $a->ID . '][promo11]" class="frame_number change-price" data-change="promo" data-col="11" value="' . (get_frame_price($a->ID)->promo11 == "" ? "0" : get_frame_price($a->ID)->promo11) . '">
                </td>
                
                 <td>
                    <span class="hidden">' . get_frame_price($a->ID)->promo12 . '</span> 
                    <input type="number" title="' . get_frame_price($a->ID)->price12_desc . '" step="1" min="0"  name="product[' . $a->ID . '][promo12]" class="frame_number change-price" data-change="promo" data-col="12" value="' . (get_frame_price($a->ID)->promo12 == "" ? "0" : get_frame_price($a->ID)->promo12) . '">
                </td>
                
                 <td>
                    <span class="hidden">' . get_frame_price($a->ID)->promo13 . '</span>
                    <input type="number" title="' . get_frame_price($a->ID)->price13_desc . '" step="1" min="0"  name="product[' . $a->ID . '][promo13]" class="frame_number change-price" data-change="promo" data-col="13" value="' . (get_frame_price($a->ID)->promo13 == "" ? "0" : get_frame_price($a->ID)->promo13) . '">
                </td>
                
                 <td>
                    <span class="hidden">' . get_frame_price($a->ID)->promo14 . '</span>
                    <input type="number" title="' . get_frame_price($a->ID)->price14_desc . '" step="1" min="0"  name="product[' . $a->ID . '][promo14]" class="frame_number change-price" data-change="promo" data-col="14" value="' . (get_frame_price($a->ID)->promo14 == "" ? "0" : get_frame_price($a->ID)->promo14) . '">
                </td>
                
                 <td>
                    <span class="hidden">' . get_frame_price($a->ID)->promo15 . '</span>
                    <input type="number" title="' . get_frame_price($a->ID)->price15_desc . '" step="1" min="0"  name="product[' . $a->ID . '][promo15]" class="frame_number change-price" data-change="promo" data-col="15" value="' . (get_frame_price($a->ID)->promo15 == "" ? "0" : get_frame_price($a->ID)->promo15) . '">
                </td>
                
                
                <td><textarea name="product[' . $a->ID . '][price0_desc]">' . get_frame_price($a->ID)->price0_desc . '</textarea></td>
                <td><textarea name="product[' . $a->ID . '][price1_desc]">' . get_frame_price($a->ID)->price1_desc . '</textarea></td>
                <td><textarea name="product[' . $a->ID . '][price2_desc]">' . get_frame_price($a->ID)->price2_desc . '</textarea></td>
                <td><textarea name="product[' . $a->ID . '][price3_desc]">' . get_frame_price($a->ID)->price3_desc . '</textarea></td>
                <td><textarea name="product[' . $a->ID . '][price4_desc]">' . get_frame_price($a->ID)->price4_desc . '</textarea></td>
                <td><textarea name="product[' . $a->ID . '][price5_desc]">' . get_frame_price($a->ID)->price5_desc . '</textarea></td>
                <td><textarea name="product[' . $a->ID . '][price6_desc]">' . get_frame_price($a->ID)->price6_desc . '</textarea></td>
                <td><textarea name="product[' . $a->ID . '][price7_desc]">' . get_frame_price($a->ID)->price7_desc . '</textarea></td>
                <td><textarea  name="product[' . $a->ID . '][price8_desc]">' . get_frame_price($a->ID)->price8_desc . '</textarea></td>
                <td><textarea name="product[' . $a->ID . '][price9_desc]">' . get_frame_price($a->ID)->price9_desc . '</textarea></td>
                <td><textarea name="product[' . $a->ID . '][price10_desc]">' . get_frame_price($a->ID)->price10_desc . '</textarea></td>
                <td><textarea name="product[' . $a->ID . '][price11_desc]">' . get_frame_price($a->ID)->price11_desc . '</textarea></td>
                <td><textarea name="product[' . $a->ID . '][price12_desc]">' . get_frame_price($a->ID)->price12_desc . '</textarea></td>
                <td><textarea name="product[' . $a->ID . '][price13_desc]">' . get_frame_price($a->ID)->price13_desc . '</textarea></td>
                <td><textarea name="product[' . $a->ID . '][price14_desc]">' . get_frame_price($a->ID)->price14_desc . '</textarea></td>
                <td><textarea name="product[' . $a->ID . '][price15_desc]">' . get_frame_price($a->ID)->price15_desc . '</textarea></td>
                
                <td><input type="text" name="product[' . $a->ID . '][pic0]" class="frame_number" value="' . get_frame_price($a->ID)->pic0 . '"></td>
                <td><input type="text" name="product[' . $a->ID . '][pic1]" class="frame_number" value="' . get_frame_price($a->ID)->pic1 . '"></td>
                <td><input type="text" name="product[' . $a->ID . '][pic2]" class="frame_number" value="' . get_frame_price($a->ID)->pic2 . '"></td>
                <td><input type="text" name="product[' . $a->ID . '][pic3]" class="frame_number" value="' . get_frame_price($a->ID)->pic3 . '"></td>
                <td><input type="text" name="product[' . $a->ID . '][pic4]" class="frame_number" value="' . get_frame_price($a->ID)->pic4 . '"></td>
                <td><input type="text" name="product[' . $a->ID . '][pic5]" class="frame_number" value="' . get_frame_price($a->ID)->pic5 . '"></td>
                <td><input type="text" name="product[' . $a->ID . '][pic6]" class="frame_number" value="' . get_frame_price($a->ID)->pic6 . '"></td>
                <td><input type="text" name="product[' . $a->ID . '][pic7]" class="frame_number" value="' . get_frame_price($a->ID)->pic7 . '"></td>
                <td><input type="text" name="product[' . $a->ID . '][pic8]" class="frame_number" value="' . get_frame_price($a->ID)->pic8 . '"></td>
                <td><input type="text" name="product[' . $a->ID . '][pic9]" class="frame_number" value="' . get_frame_price($a->ID)->pic9 . '"></td>
                <td><input type="text" name="product[' . $a->ID . '][pic10]" class="frame_number" value="' . get_frame_price($a->ID)->pic10 . '"></td>
                <td><input type="text" name="product[' . $a->ID . '][pic11]" class="frame_number" value="' . get_frame_price($a->ID)->pic11 . '"></td>
                <td><input type="text" name="product[' . $a->ID . '][pic12]" class="frame_number" value="' . get_frame_price($a->ID)->pic12 . '"></td>
                <td><input type="text" name="product[' . $a->ID . '][pic13]" class="frame_number" value="' . get_frame_price($a->ID)->pic13 . '"></td>
                <td><input type="text" name="product[' . $a->ID . '][pic14]" class="frame_number" value="' . get_frame_price($a->ID)->pic14 . '"></td>
                <td><input type="text" name="product[' . $a->ID . '][pic15]" class="frame_number" value="' . get_frame_price($a->ID)->pic15 . '"></td>
                 <td><textarea name="product[' . $a->ID . '][description]">' . get_frame_price($a->ID)->description . '</textarea></td>
            </tr>';
          }
        }
      }
    }

    // echo $num;



    if (isset($_POST["save"])) {
      global $wpdb;
      $servername = "localhost";
      $dbname = $wpdb->dbname;
      $username = $wpdb->dbuser;
      $password = $wpdb->dbpassword;


      // Create connection
      $conn = new mysqli($servername, $username, $password, $dbname);
      // Check connection
      if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
      }
      
      $conn->set_charset("utf8mb4");


      $product = wp_unslash($_POST['product']);

      foreach ($product as $key => $el) {
        // query

        $q = mysqli_prepare($conn, "SELECT product_id FROM frame_prices WHERE product_id = ?");
        mysqli_stmt_bind_param($q, 'i', $key);
        mysqli_stmt_execute($q);
        $result = mysqli_stmt_get_result($q);
        $q->close();

        $br = 0;
        while ($row = mysqli_fetch_assoc($result)) {
          $number_products = $row;
          $br++;
        }

        if ($br > 0) {
          $q = mysqli_prepare($conn, "UPDATE frame_prices SET
  price1= ?,
  price2= ?,
  price3= ?,
  price4= ?,
  price5= ?,
  price6= ?,
  price7= ?,
  price8= ?,
  price9= ?, 
  price10= ?,
  price11= ?,
  price12= ?,
  price13= ?,
  price14= ?,
  price15= ?,
  promo1= ?,
    promo2= ?,
    promo3= ?,
    promo4= ?,
    promo5= ?,
    promo6= ?,
    promo7= ?,
    promo8= ?,
    promo9= ?,
    promo10= ?,
    promo11= ?,
    promo12= ?,
    promo13= ?,
    promo14= ?,
    promo15= ?,
    price0_desc=?,
    price1_desc=?,
    price2_desc=?,
    price3_desc=?,
    price4_desc=?,
    price5_desc=?,
    price6_desc=?,
    price7_desc=?,
    price8_desc=?,
    price9_desc=?,
    price10_desc=?,
    price11_desc=?,
    price12_desc=?,
    price13_desc=?,
    price14_desc=?,
    price15_desc=?,
    pic0= ?,
    pic1= ?,
    pic2= ?,
    pic3= ?,
    pic4= ?,
    pic5= ?,
    pic6= ?,
    pic7= ?,
    pic8= ?,
    pic9= ?,
    pic10= ?,
    pic11= ?,
    pic12= ?,
    pic13= ?,
    pic14= ?,
    pic15= ?,
    description= ?
  WHERE product_id = ?");
          mysqli_stmt_bind_param(
            $q,
            "ddddddddddddddddddddddddddddddsssssssssssssssssssssssssssssssssi",
            $el['price1'],
            $el['price2'],
            $el['price3'],
            $el['price4'],
            $el['price5'],
            $el['price6'],
            $el['price7'],
            $el['price8'],
            $el['price9'],
            $el['price10'],
            $el['price11'],
            $el['price12'],
            $el['price13'],
            $el['price14'],
            $el['price15'],
            $el['promo1'],
            $el['promo2'],
            $el['promo3'],
            $el['promo4'],
            $el['promo5'],
            $el['promo6'],
            $el['promo7'],
            $el['promo8'],
            $el['promo9'],
            $el['promo10'],
            $el['promo11'],
            $el['promo12'],
            $el['promo13'],
            $el['promo14'],
            $el['promo15'],
            $el['price0_desc'],
            $el['price1_desc'],
            $el['price2_desc'],
            $el['price3_desc'],
            $el['price4_desc'],
            $el['price5_desc'],
            $el['price6_desc'],
            $el['price7_desc'],
            $el['price8_desc'],
            $el['price9_desc'],
            $el['price10_desc'],
            $el['price11_desc'],
            $el['price12_desc'],
            $el['price13_desc'],
            $el['price14_desc'],
            $el['price15_desc'],
            $el['pic0'],
            $el['pic1'],
            $el['pic2'],
            $el['pic3'],
            $el['pic4'],
            $el['pic5'],
            $el['pic6'],
            $el['pic7'],
            $el['pic8'],
            $el['pic9'],
            $el['pic10'],
            $el['pic11'],
            $el['pic12'],
            $el['pic13'],
            $el['pic14'],
            $el['pic15'],
            $el['description'],
            $key
          );
          mysqli_stmt_execute($q);
          $changedRows += $q->affected_rows;
          $q->close();
        } else {

          $q = mysqli_prepare($conn, "INSERT INTO frame_prices (
price1,
  price2,
  price3,
  price4,
  price5,
  price6,
  price7,
  price8,
  price9,
  price10,
  price11,
  price12,
  price13,
  price14,
  price15,
  promo1,
    promo2,
    promo3,
    promo4,
    promo5,
    promo6,
    promo7,
    promo8,
    promo9,
    promo10,
    promo11,
    promo12,
    promo13,
    promo14,
    promo15,
    price0_desc,
    price1_desc,
    price2_desc,
    price3_desc,
    price4_desc,
    price5_desc,
    price6_desc,
    price7_desc,
    price8_desc,
    price9_desc,
    price10_desc,
    price11_desc,
    price12_desc,
    price13_desc,
    price14_desc,
    price15_desc,
    pic0,
    pic1,
    pic2,
    pic3,
    pic4,
    pic5,
    pic6,
    pic7,
    pic8,
    pic9,
    pic10,
    pic11,
    pic12,
    pic13,
    pic14,
    pic15,
    description,
    product_id
        ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
          mysqli_stmt_bind_param(
            $q,
            "ddddddddddddddddddddddddddddddsssssssssssssssssssssssssssssssssi",
            $el['price1'],
            $el['price2'],
            $el['price3'],
            $el['price4'],
            $el['price5'],
            $el['price6'],
            $el['price7'],
            $el['price8'],
            $el['price9'],
            $el['price10'],
            $el['price11'],
            $el['price12'],
            $el['price13'],
            $el['price14'],
            $el['price15'],
            $el['promo1'],
            $el['promo2'],
            $el['promo3'],
            $el['promo4'],
            $el['promo5'],
            $el['promo6'],
            $el['promo7'],
            $el['promo8'],
            $el['promo9'],
            $el['promo10'],
            $el['promo11'],
            $el['promo12'],
            $el['promo13'],
            $el['promo14'],
            $el['promo15'],
            $el['price0_desc'],
            $el['price1_desc'],
            $el['price2_desc'],
            $el['price3_desc'],
            $el['price4_desc'],
            $el['price5_desc'],
            $el['price6_desc'],
            $el['price7_desc'],
            $el['price8_desc'],
            $el['price9_desc'],
            $el['price10_desc'],
            $el['price11_desc'],
            $el['price12_desc'],
            $el['price13_desc'],
            $el['price14_desc'],
            $el['price15_desc'],
            $el['pic0'],
            $el['pic1'],
            $el['pic2'],
            $el['pic3'],
            $el['pic4'],
            $el['pic5'],
            $el['pic6'],
            $el['pic7'],
            $el['pic8'],
            $el['pic9'],
            $el['pic10'],
            $el['pic11'],
            $el['pic12'],
            $el['pic13'],
            $el['pic14'],
            $el['pic15'],
            $el['description'],
            $key

          );
          mysqli_stmt_execute($q);
          $changedRows += $q->affected_rows;
          $q->close();
        }
      }




      ob_start();
      header('Location: ' . $_SERVER['HTTP_REFERER'] . '&rows=' . $changedRows);
    }


    echo '</table>
	<button name="save" style="position: fixed;color: white;padding: 10px;background: green;bottom: 5px;right: 5px;z-index: 99;">Запази</button>
	</form>
	
	<span class="dashicons dashicons-admin-tools show-options-button"></span>
	
	';


    ?>

  </div>

<?php

  // $_SESSION['changed_rows'] = '';
} // end of function 




//Цени на каси НАЧАЛО
add_filter('woocommerce_product_tabs', 'my_shipping_tab');
function my_shipping_tab($tabs)
{
  global $product;
  global $wpdb;
  $product_id = $product->id;
  $result = $wpdb->get_results("SELECT * FROM frame_prices WHERE product_id = '" . $product_id . "'");
  if (count($result) > 0) {
      
    wp_register_script('main_js', plugins_url('main.js', __FILE__));
    wp_enqueue_script('main_js');
    
    wp_register_style('style.css', plugins_url('style.css', __FILE__));
    wp_enqueue_style('style.css');

    $tabs['shipping'] = array(
      'title'     => __('Цени според касата', 'child-theme'),
      'priority'  => 31,
      'callback'  => 'my_shipping_tab_callback'
    );
  }
  return $tabs;
}

function my_shipping_tab_callback()
{
  global $product;
  global $wpdb;
  global $plugin_type_name;
  $product_id = $product->id;
  $result = $wpdb->get_results("SELECT * FROM frame_prices WHERE product_id = '" . $product_id . "'");

  function prc($price)
  {
    return number_format($price, 2) . 'лв.';
  }

  if (count($result) > 0) {
    echo '<div class="container-kasi"><table class="kasi-table"><tbody>
                    <tr>
                        <th>' . $plugin_type_name . '</th>
                        <th>Описание</th>
                        <th>Цена</th>
                    </tr>';
    foreach ($result as $row) {
      echo (mb_strlen($row->price0_desc) > 0 ? '<tr>
        <td data-th="Каса"><img src="/ceni/' . $row->pic0 . '"></td>
        <td data-th="Описание">' . $row->price0_desc . '</td>
        <td data-th="Цена" class="price-css">' . ($product->sale_price > 0 ? prc($product->price) . ' <del>' . prc($product->regular_price) . '</del>' : prc($product->price)) . '</td>
      </tr>' : '');


      echo ($row->price1 > 0 ? '                    
                    <tr>
                    <td data-th="Каса"><img src="/ceni/' . $row->pic1 . '"></td>
                        <td data-th="Описание">' . $row->price1_desc . '</td>
                        <td data-th="Цена" class="price-css">' . ($row->promo1 > 0 ? prc($row->promo1) . ' <del>' . prc($row->price1) . '</del>' : prc($row->price1)) . '</td>
                    </tr>' : '') . '
                    ' . ($row->price2 > 0 ? '<tr>
                    <td data-th="Каса"><img src="/ceni/' . $row->pic2 . '"></td>
                        <td data-th="Описание">' . $row->price2_desc . '</td>
                        <td data-th="Цена" class="price-css">' . ($row->promo2 > 0 ? prc($row->promo2) . ' <del>' . prc($row->price2) . '</del>' : prc($row->price2)) . '</td>
                    </tr>' : '') . '
                    ' . ($row->price3 > 0 ? '<tr>
                    <td data-th="Каса"><img src="/ceni/' . $row->pic3 . '"></td>
                        <td data-th="Описание">' . $row->price3_desc . '</td>
                        <td data-th="Цена" class="price-css">' . ($row->promo3 > 0 ? prc($row->promo3) . ' <del>' . prc($row->price3) . '</del>' : prc($row->price3)) . '</td>
                    </tr>' : '') . '
                    ' . ($row->price4 > 0 ? '<tr>
                    <td data-th="Каса"><img src="/ceni/' . $row->pic4 . '"></td>
                        <td data-th="Описание">' . $row->price4_desc . '</td>
                        <td data-th="Цена" class="price-css">' . ($row->promo4 > 0 ? prc($row->promo4) . ' <del>' . prc($row->price4) . '</del>' : prc($row->price4)) . '</td>
                    </tr>' : '') . '
                    ' . ($row->price5 > 0 ? '<tr>
                    <td data-th="Каса"><img src="/ceni/' . $row->pic5 . '"></td>
                        <td data-th="Описание">' . $row->price5_desc . '</td>
                        <td data-th="Цена" class="price-css">' . ($row->promo5 > 0 ? prc($row->promo5) . ' <del>' . prc($row->price5) . '</del>' : prc($row->price5)) . '</td>
                    </tr>' : '') . '
                    ' . ($row->price6 > 0 ? '<tr>
                    <td data-th="Каса"><img src="/ceni/' . $row->pic6 . '"></td>
                        <td data-th="Описание">' . $row->price6_desc . '</td>
                        <td data-th="Цена" class="price-css">' . ($row->promo6 > 0 ? prc($row->promo6) . ' <del>' . prc($row->price6) . '</del>' : prc($row->price6)) . '</td>
                    </tr>' : '') . '
                    ' . ($row->price7 > 0 ? '<tr>
                    <td data-th="Каса"><img src="/ceni/' . $row->pic7 . '"></td>
                        <td data-th="Описание">' . $row->price7_desc . '</td>
                        <td data-th="Цена" class="price-css">' . ($row->promo7 > 0 ? prc($row->promo7) . ' <del>' . prc($row->price7) . '</del>' : prc($row->price7)) . '</td>
                    </tr>' : '') . '
                    ' . ($row->price8 > 0 ? '<tr>
                    <td data-th="Каса"><img src="/ceni/' . $row->pic8 . '"></td>
                        <td data-th="Описание">' . $row->price8_desc . '</td>
                        <td data-th="Цена" class="price-css">' . ($row->promo8 > 0 ? prc($row->promo8) . ' <del>' . prc($row->price8) . '</del>' : prc($row->price8)) . '</td>
                    </tr>' : '') . '
                    ' . ($row->price9 > 0 ? '<tr>
                    <td data-th="Каса"><img src="/ceni/' . $row->pic9 . '"></td>
                        <td data-th="Описание">' . $row->price9_desc . '</td>
                        <td data-th="Цена" class="price-css">' . ($row->promo9 > 0 ? prc($row->promo9) . ' <del>' . prc($row->price9) . '</del>' : prc($row->price9)) . '</td>
                    </tr>' : '') . '
                    ' . ($row->price10 > 0 ? '<tr>
                    <td data-th="Каса"><img src="/ceni/' . $row->pic10 . '"></td>
                        <td data-th="Описание">' . $row->price10_desc . '</td>
                        <td data-th="Цена" class="price-css">' . ($row->promo10 > 0 ? prc($row->promo10) . ' <del>' . prc($row->price10) . '</del>' : prc($row->price10)) . '</td>
                    </tr>' : '') . '
                     ' . ($row->price11 > 0 ? '<tr>
                    <td data-th="Каса"><img src="/ceni/' . $row->pic11 . '"></td>
                        <td data-th="Описание">' . $row->price11_desc . '</td>
                        <td data-th="Цена" class="price-css">' . ($row->promo11 > 0 ? prc($row->promo11) . ' <del>' . prc($row->price11) . '</del>' : prc($row->price11)) . '</td>
                    </tr>' : '') . '
                     ' . ($row->price12 > 0 ? '<tr>
                    <td data-th="Каса"><img src="/ceni/' . $row->pic12 . '"></td>
                        <td data-th="Описание">' . $row->price12_desc . '</td>
                        <td data-th="Цена" class="price-css">' . ($row->promo12 > 0 ? prc($row->promo12) . ' <del>' . prc($row->price12) . '</del>' : prc($row->price12)) . '</td>
                    </tr>' : '') . '
                     ' . ($row->price13 > 0 ? '<tr>
                    <td data-th="Каса"><img src="/ceni/' . $row->pic13 . '"></td>
                        <td data-th="Описание">' . $row->price13_desc . '</td>
                        <td data-th="Цена" class="price-css">' . ($row->promo13 > 0 ? prc($row->promo13) . ' <del>' . prc($row->price13) . '</del>' : prc($row->price13)) . '</td>
                    </tr>' : '') . '
                     ' . ($row->price14 > 0 ? '<tr>
                    <td data-th="Каса"><img src="/ceni/' . $row->pic14 . '"></td>
                        <td data-th="Описание">' . $row->price14_desc . '</td>
                        <td data-th="Цена" class="price-css">' . ($row->promo14 > 0 ? prc($row->promo14) . ' <del>' . prc($row->price14) . '</del>' : prc($row->price14)) . '</td>
                    </tr>' : '') . '
                    ' . ($row->price15 > 0 ? '<tr>
                    <td data-th="Каса"><img src="/ceni/' . $row->pic15 . '"></td>
                        <td data-th="Описание">' . $row->price15_desc . '</td>
                        <td data-th="Цена" class="price-css">' . ($row->promo15 > 0 ? prc($row->promo15) . ' <del>' . prc($row->price15) . '</del>' : prc($row->price15)) . '</td>
                    </tr>' : '') . '
                ';
    }
    echo '</table>';
    echo $result[0]->description;
  }
}
//Цени на каси КРАЙ


?>