<?PHP 

//Настройки 

$kp_config = array (
  'settings' => 
  array (
    'kinopoiskdev' => '',
    'max_facts' => 10,
    'fact_prefix' => '<li>',
    'fact_sufix' => '</li><br />',
    'max_errors' => 5,
    'errors_prefix' => '<li>',
    'errors_sufix' => '</li><br />',
    'date_format' => 2,
    'max_directors' => 5,
    'max_actors' => 10,
    'max_producers' => 5,
    'max_screenwriters' => 5,
    'max_operators' => 5,
    'max_composers' => 5,
    'max_design' => 5,
    'max_editors' => 5,
    'tabus' => '',
  ),
  'fields' => 
  array (
    'xf_kinopoisk_id' => '',
  ),
  'xfields' => 
  array (
    'title' => '[if_russian]{russian}[/if_russian][ifnot_russian]{original}[/ifnot_russian]',
    'short_story' => '{plot}',
    'alt_name' => '[if_original]{original}[/if_original][ifnot_original]{russian}[/ifnot_original]',
    'catalog' => '[if_catalog_ru]{catalog_ru}[/if_catalog_ru][ifnot_catalog_ru]{catalog_eng}[/ifnot_catalog_ru]',
  ),
  'images' => 
  array (
    'screens_count' => 10,
  ),
);

?>
