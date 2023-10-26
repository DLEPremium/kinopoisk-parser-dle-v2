<?php

/*
=====================================================
 Copyright (c) 2023 MrDeath
=====================================================
 This code is protected by copyright
=====================================================
*/

if( ! defined( 'DATALIFEENGINE' ) ) {
	die( "Hacking attempt!" );
}

if ($parse_action == 'search') {
    
    if ($kinopoisk_id) $kp_api = api_request('https://api.kinopoisk.dev/v1.3/movie/'.$kinopoisk_id, $kp_config['settings']['kinopoiskdev']);
    elseif ($search_name) $kp_api = api_request('https://api.kinopoisk.dev/v1.2/movie/search?page=1&limit=10&query='.$search_name, $kp_config['settings']['kinopoiskdev']);

    if ( $kp_api['docs'] && $search_name ) {
        
        foreach ( $kp_api['docs'] as $result ) {
            $info = '';
            $countries = $genres = [];
            if ( $result['countries'] ) {
                foreach ( $result['countries'] as $country ) {
                    $countries[] = $country;
                }
                $info = 'Страна - '.implode(', ', $countries).'. ';
            }
            if ( $result['genres'] ) {
                foreach ( $result['genres'] as $genre ) {
                    $genres[] = $genre;
                }
                $info .= 'Жанры - '.implode(', ', $genres);
            }

            if ( $result['id'] ) $kp_link = 'https://www.kinopoisk.ru/film/'.$result['id'].'/';
            else $kp_link = "";
            $imdb_link = "";
            $edit_link = '';
            
            $where = "xfields LIKE '%".$kp_config['fields']['xf_kinopoisk_id']."|".$result['id']."||%'";
            $proverka = $db->super_query( "SELECT id, xfields FROM " . PREFIX . "_post WHERE ".$where );
	    	if ($proverka) {
	    	    $find_id = "est";
	    	    $edit_link = $config['http_home_url'].'admin.php?mod=editnews&action=editnews&id='.$proverka['id'];
	    	}
	    	else $find_id = "net";
            
            $responseArray[] = array(
				'kp_id' => $result['id'],
				'title' => $result['name'] ? $result['name'] : '',
				'orig_title' => $result['alternativeName'] ? $result['alternativeName'] : '',
				'poster' => $result['poster'] ? $result['poster'] : '',
				'year' => $result['year'] ? $result['year'] : '',
				'kind' => $cat_type[$result['type']],
				'info' => $info,
				'plot' => $result['description'] ? $result['description'] : '',
				'kp_link' => $kp_link,
				'imdb_link' => $imdb_link,
				'find_id' => $find_id,
				'edit_link' => $edit_link
			);
        }
        
    }
    elseif ( $kp_api['id'] ) {
			$info = '';
            $countries = $genres = [];
            if ( $kp_api['countries'] ) {
                foreach ( $kp_api['countries'] as $country ) {
                    $countries[] = $country['name'];
                }
                $info = 'Страна - '.implode(', ', $countries).'. ';
            }
            if ( $kp_api['genres'] ) {
                foreach ( $kp_api['genres'] as $genre ) {
                    $genres[] = $genre['name'];
                }
                $info .= 'Жанры - '.implode(', ', $genres);
            }
            
            if ( $kp_api['id'] ) $kp_link = 'https://www.kinopoisk.ru/film/'.$kp_api['id'].'/';
            else $kp_link = "";
            $imdb_link = "";
            
            $where = "xfields LIKE '%".$kp_config['fields']['xf_kinopoisk_id']."|".$kp_api['id']."||%'";
            $proverka = $db->super_query( "SELECT id, xfields FROM " . PREFIX . "_post WHERE ".$where );
	    	if ($proverka) {
	    	    $find_id = "est";
	    	    $edit_link = $config['http_home_url'].'admin.php?mod=editnews&action=editnews&id='.$proverka['id'];
	    	}
	    	else $find_id = "net";
            
            $responseArray[] = array(
				'kp_id' => $kp_api['id'],
				'title' => $kp_api['name'] ? $kp_api['name'] : '',
				'orig_title' => $kp_api['alternativeName'] ? $kp_api['alternativeName'] : '',
				'poster' => $kp_api['poster']['url'] ? $kp_api['poster']['url'] : '',
				'year' => $kp_api['year'] ? $kp_api['year'] : '',
				'kind' => $cat_type[$kp_api['type']],
				'info' => $info,
				'plot' => $kp_api['description'] ? $kp_api['description'] : '',
				'kp_link' => $kp_link,
				'imdb_link' => $imdb_link,
				'find_id' => $find_id,
				'edit_link' => $edit_link
			);
    }
    
}
elseif ($parse_action == 'parse') {
    
    $array_data = array();
    
    $kp_api = api_request('https://api.kinopoisk.dev/v1.3/movie/'.$kp_id, $kp_config['settings']['kinopoiskdev']);
    
    foreach ( $kp_api as $api_name => $api_value ) {
        if ( $api_value === NULL || $api_value === false ) $kp_api[$api_name] = '';
    }
	
	$kp_api_fields = ['imdb_id', 'tmdb_id', 'russian', 'original', 'english', 'poster', 'cover', 'logo', 'rating_kinopoisk', 'votes_kinopoisk', 'rating_imdb', 'votes_imdb', 'rating_film_critics', 'rating_film_critics_vote_count', 'rating_await', 'rating_await_count', 'rating_rf_critics', 'rating_rf_critics_vote_count', 'year', 'duration', 'slogan', 'plot', 'short_plot', 'status_en', 'status_ru', 'type_en', 'type_ru', 'rating_mpaa', 'rating_age_limits', 'countries', 'genres', 'start_year', 'end_year', 'season', 'episode', 'facts', 'errors', 'world_premier', 'russia_premier', 'budget', 'fees_usa', 'fees_world', 'youtube_trailer', 'studio', 'networks', 'distributors', 'lists', 'directors', 'actors', 'producers', 'screenwriters', 'operators', 'composers', 'design', 'editors', 'voice_actor', 'sequels', 'similar', 'top10', 'top10_tv', 'top250', 'top250_tv'];
	
	if ( $kp_api['externalId']['imdb'] ) $array_data['imdb_id'] = $kp_api['externalId']['imdb'];
	if ( $kp_api['externalId']['tmdb'] ) $array_data['tmdb_id'] = $kp_api['externalId']['tmdb'];
	if ( $kp_api['name'] ) $array_data['russian'] = $kp_api['name'];
	if ( $kp_api['alternativeName'] ) $array_data['original'] = $kp_api['alternativeName'];
	if ( $kp_api['enName'] ) $array_data['english'] = $kp_api['enName'];
	if ( $kp_api['type'] ) {
        $array_data['type_en'] = $cat_type_en[$kp_api['type']];
	    $array_data['type_ru'] = $cat_type[$kp_api['type']];
	}
	if ( $kp_api['year'] ) $array_data['year'] = $kp_api['year'];
	if ( $kp_api['description'] ) $array_data['plot'] = $kp_api['description'];
	if ( $kp_api['shortDescription'] ) $array_data['short_plot'] = $kp_api['shortDescription'];
	if ( $kp_api['slogan'] ) $array_data['slogan'] = $kp_api['slogan'];
	if ( $kp_api['rating']['kp'] ) $array_data['rating_kinopoisk'] = $kp_api['rating']['kp'];
	if ( $kp_api['votes']['kp'] ) $array_data['votes_kinopoisk'] = $kp_api['votes']['kp'];
	if ( $kp_api['rating']['imdb'] ) $array_data['rating_imdb'] = $kp_api['rating']['imdb'];
	if ( $kp_api['votes']['imdb'] ) $array_data['votes_imdb'] = $kp_api['votes']['imdb'];
	if ( $kp_api['rating']['filmCritics'] ) $array_data['rating_film_critics'] = $kp_api['rating']['filmCritics'];
	if ( $kp_api['votes']['filmCritics'] ) $array_data['rating_film_critics_vote_count'] = $kp_api['votes']['filmCritics'];
	if ( $kp_api['rating']['await'] ) $array_data['rating_await'] = $kp_api['rating']['await'];
	if ( $kp_api['votes']['await'] ) $array_data['rating_await_count'] = $kp_api['votes']['await'];
	if ( $kp_api['rating']['russianFilmCritics'] ) $array_data['rating_rf_critics'] = $kp_api['rating']['russianFilmCritics'];
	if ( $kp_api['votes']['russianFilmCritics'] ) $array_data['rating_rf_critics_vote_count'] = $kp_api['votes']['russianFilmCritics'];
	if ( $kp_api['movieLength'] || $kp_api['seriesLength'] ) $array_data['duration'] = $kp_api['movieLength'] ? $kp_api['movieLength'] : $kp_api['seriesLength'];
	if ( $kp_api['ratingMpaa'] ) $array_data['rating_mpaa'] = $kp_api['ratingMpaa'];
	if ( $kp_api['ageRating'] ) $array_data['rating_age_limits'] = $kp_api['ageRating'];
	if ( $kp_api['poster']['url'] ) $array_data['poster'] = $kp_api['poster']['url'];
	if ( $kp_api['backdrop']['url'] ) $array_data['cover'] = $kp_api['backdrop']['url'];
	if ( $kp_api['genres'] ) {
	    $genres = [];
	    foreach ( $kp_api['genres'] as $genre ) {
	        $genres[] = $genre['name'];
	    }
	    $array_data['genres'] = implode(', ', $genres);
	}
	else $array_data['genres'] = "";
	if ( $kp_api['countries'] ) {
	    $countries = [];
	    foreach ( $kp_api['countries'] as $country ) {
	        $countries[] = $country['name'];
	    }
	    $array_data['countries'] = implode(', ', $countries);
	}
	else $array_data['countries'] = "";
	
	if ( $kp_api['persons'] ) {
	    $directors = $actors = $producers = $screenwriters = $operators = $composers = $design = $editors = $voice_actor = [];
	    foreach ( $kp_api['persons'] as $staff ) {
	        if ( $staff['profession'] == 'режиссеры' ) $directors[] = $staff['name'] ? $staff['name'] : $staff['enName'];
	        elseif ( $staff['profession'] == 'актеры' ) $actors[] = $staff['name'] ? $staff['name'] : $staff['enName'];
	        elseif ( $staff['profession'] == 'продюсеры' ) $producers[] = $staff['name'] ? $staff['name'] : $staff['enName'];
	        elseif ( $staff['profession'] == 'редакторы' ) $screenwriters[] = $staff['name'] ? $staff['name'] : $staff['enName'];
	        elseif ( $staff['profession'] == 'операторы' ) $operators[] = $staff['name'] ? $staff['name'] : $staff['enName'];
	        elseif ( $staff['profession'] == 'композиторы' ) $composers[] = $staff['nameRu'] ? $staff['name'] : $staff['enName'];
	        elseif ( $staff['profession'] == 'художники' ) $design[] = $staff['name'] ? $staff['name'] : $staff['enName'];
	        elseif ( $staff['profession'] == 'монтажеры' ) $editors[] = $staff['name'] ? $staff['name'] : $staff['enName'];
	        elseif ( $staff['profession'] == 'актеры дубляжа' ) $voice_actor[] = $staff['name'] ? $staff['name'] : $staff['enName'];
	    }
	    if ( $kp_config['settings']['max_directors'] && $directors ) {
	        $directors = array_slice($directors, 0, $kp_config['settings']['max_directors']);
	        $array_data['directors'] = implode(', ', $directors);
	    }
	    if ( $kp_config['settings']['max_actors'] && $actors ) {
	        $actors = array_slice($actors, 0, $kp_config['settings']['max_actors']);
	        $array_data['actors'] = implode(', ', $actors);
	    }
	    if ( $kp_config['settings']['max_producers'] && $producers ) {
	        $producers = array_slice($producers, 0, $kp_config['settings']['max_producers']);
	        $array_data['producers'] = implode(', ', $producers);
	    }
	    if ( $kp_config['settings']['max_screenwriters'] && $screenwriters ) {
	        $screenwriters = array_slice($screenwriters, 0, $kp_config['settings']['max_screenwriters']);
	        $array_data['screenwriters'] = implode(', ', $screenwriters);
	    }
	    if ( $kp_config['settings']['max_operators'] && $operators ) {
	        $operators = array_slice($operators, 0, $kp_config['settings']['max_operators']);
	        $array_data['operators'] = implode(', ', $operators);
	    }
	    if ( $kp_config['settings']['max_composers'] && $composers ) {
	        $composers = array_slice($composers, 0, $kp_config['settings']['max_composers']);
	        $array_data['composers'] = implode(', ', $composers);
	    }
	    if ( $kp_config['settings']['max_design'] && $design ) {
	        $design = array_slice($design, 0, $kp_config['settings']['max_design']);
	        $array_data['design'] = implode(', ', $design);
	    }
	    if ( $kp_config['settings']['max_editors'] && $editors ) {
	        $editors = array_slice($editors, 0, $kp_config['settings']['max_editors']);
	        $array_data['editors'] = implode(', ', $editors);
	    }
	    if ( $kp_config['settings']['max_voice_actor'] && $voice_actor ) {
	        $voice_actor = array_slice($voice_actor, 0, $kp_config['settings']['max_voice_actor']);
	        $array_data['voice_actor'] = implode(', ', $voice_actor);
	    }
	}
	
	if ( $kp_api['sequelsAndPrequels'] ) {
	    $sequels = [];
	    foreach ( $kp_api['sequelsAndPrequels'] as $sequel ) {
	        $sequels[] = $sequel['id'];
	    }
	    $sequels = array_unique($sequels);
	    $array_data['sequels'] = implode(',', $sequels);
	}
	
	if ( $kp_api['facts'] ) {
	    $errors_arr = $facts_arr = [];
	    foreach ( $kp_api['facts'] as $facts_errors ) {
	        if ( $facts_errors['type'] == 'FACT' ) $facts_arr[] = $kp_config['settings']['fact_prefix'].strip_tags($facts_errors['value']).$kp_config['settings']['fact_sufix'];
	        elseif ( $facts_errors['type'] ) $errors_arr[] = $kp_config['settings']['errors_prefix'].strip_tags($facts_errors['value']).$kp_config['settings']['errors_sufix'];
	    }
	    if ( $kp_config['settings']['max_facts'] && $facts_arr) {
	        $facts_arr = array_slice($facts_arr, 0, $kp_config['settings']['max_facts']);
	        $array_data['facts'] = implode('', $facts_arr);
	    }
	        
	    if ( $kp_config['settings']['max_errors'] && $errors_arr ) {
	        $errors_arr = array_slice($errors_arr, 0, $kp_config['settings']['max_errors']);
	        $array_data['errors'] = implode('', $errors_arr);
	    }
	}
	
	if ( $kp_api['similarMovies'] ) {
	    $similars = [];
	    foreach ( $kp_api['similarMovies'] as $similar ) {
	        $similars[] = $similar['id'];
	    }
	    $similars = array_unique($similars);
	    $array_data['similar'] = implode(',', $similars);
	}
	
	if ( $kp_api['videos']['trailers'] ) {
	    foreach ( $kp_api['videos']['trailers'] as $videos ) {
	        if ( $videos['site'] == 'youtube' && stripos($videos['type'], 'TRAILER') !== false ) {
	            if( preg_match( "#youtu.be/(.*)#i", $videos['url'], $match ) ) $array_data['youtube_trailer'] = 'https://www.youtube.com/embed/'.trim($match[1]);
	            elseif( preg_match( "#youtube.com/v/(.*)#i", $videos['url'], $match ) ) $array_data['youtube_trailer'] = 'https://www.youtube.com/embed/'.trim($match[1]);
	            elseif( preg_match( "#youtube.com/watch?v=(.*)#i", $videos['url'], $match ) ) $array_data['youtube_trailer'] = 'https://www.youtube.com/embed/'.trim($match[1]);
	        }
	    }
	}
	
	if ( $kp_api['premiere']['world'] ) $array_data['world_premier'] = date('d.m.Y', strtotime($kp_api['premiere']['world']));
	if ( $kp_api['premiere']['russia'] ) $array_data['russia_premier'] = date('d.m.Y', strtotime($kp_api['premiere']['russia']));
	if ( $kp_api['logo']['url'] ) $array_data['logo'] = $kp_api['logo']['url'];
	$array_data['top10'] = $kp_api['top10'] && $kp_api['isSeries'] === false ? $kp_api['top10'] : '';
	$array_data['top10_tv'] = $kp_api['top10'] && $kp_api['isSeries'] === true ? $kp_api['top10'] : '';
	$array_data['top250'] = $kp_api['top10'] && $kp_api['isSeries'] === false ? $kp_api['top10'] : '';
	$array_data['top250_tv'] = $kp_api['top10'] && $kp_api['isSeries'] === true ? $kp_api['top10'] : '';
	
	if ( $kp_api['lists'] ) {
	    $lists = [];
	    foreach ( $kp_api['lists'] as $list ) {
	        $lists[] = $list;
	    }
	    $lists = array_unique($lists);
	    $array_data['lists'] = implode(', ', $lists);
	}
	
	if ( $kp_api['productionCompanies'] ) {
	    $companies = [];
	    foreach ( $kp_api['productionCompanies'] as $companie ) {
	        $companies[] = $companie['name'];
	    }
	    $companies = array_unique($companies);
	    $array_data['studio'] = implode(', ', $companies);
	}
	
	if ( $kp_api['networks'] ) {
	    $networks = [];
	    foreach ( $kp_api['networks'] as $network ) {
	        $networks[] = $network['name'];
	    }
	    $networks = array_unique($networks);
	    $array_data['networks'] = implode(', ', $networks);
	}
	
	$array_data['distributors'] = $kp_api['distributors']['distributor'] ? $kp_api['distributors']['distributor'] : '';
	
	if ( $kp_api['seasonsInfo']  ) {
	    
	    $last_season = 0;
	    $last_episode = '';
	    
	    foreach ( $kp_api['seasonsInfo'] as $seasons ) {
	        if ( $last_season < $seasons['number'] ) {
	            $last_season = $seasons['number'];
	            $last_episode = $seasons['episodesCount'];
	        }
	        $array_data['season'] = $last_season;
	        $array_data['episode'] = $last_episode;
	    }
	    
	}
	
	if ( $kp_api['status']  ) {
	    $array_data['status_en'] = $kp_api['status'];
	    $array_data['status_ru'] = $statuses[$kp_api['status']];
	}
	
	$array_data['start_year'] = $kp_api['releaseYears']['start'] ? $kp_api['releaseYears']['start'] : '';
	$array_data['end_year'] = $kp_api['releaseYears']['end'] ? $kp_api['releaseYears']['end'] : '';
	
	$array_data['fees_usa'] = $kp_api['fees']['usa']['value'] ? $kp_api['fees']['usa']['value'] : '';
	$array_data['fees_world'] = $kp_api['fees']['world']['value'] ? $kp_api['fees']['world']['value'] : '';
	$array_data['budget'] = $kp_api['budget']['value'] ? $kp_api['budget']['value'] : '';
	
	foreach ( $kp_api_fields as $kp_field ) {
		if ( !$array_data[$kp_field] ) $array_data[$kp_field] = '';
	}
	
	$kp_data = file_get_contents('https://rating.kinopoisk.ru/'.$kp_id.'.xml');
    preg_match_all("|<kp_rating num_vote=\"(.*)\">(.*)</kp_rating><imdb_rating num_vote=\"(.*)\">(.*)</imdb_rating>|U", $kp_data, $ratng);
    if ( $ratng[1][0] ) $array_data['votes_kinopoisk'] = $ratng[1][0];
    if ( $ratng[2][0] ) $array_data['rating_kinopoisk'] = $ratng[2][0];
    if ( $ratng[3][0] ) $array_data['votes_imdb'] = $ratng[3][0];
    if ( $ratng[4][0] ) $array_data['rating_imdb'] = $ratng[4][0];
    
    if ( isset($russia_premier) ) $world_premier = $russia_premier;
    if ( isset($world_premier) ) {
		$time_today = date( "Y-m-d", time() );
        if ( strtotime($world_premier) > strtotime($time_today) ) $array_data['status'] = 'анонсировано';
        else $array_data['status'] = 'вышло';
    }
    else $array_data['status'] = '';
    
}
