<?php

// defined( 'ABSPATH' ) || exit;
// /**
//  * Get metadata
//  *
//  * @param $post_id
//  * @param $key
//  * @param string $default
//  *
//  * @return mixed|string
//  * @since 1.0.0
//  */
// function radio_player_get_meta( $post_id, $key, $default = '' )
// {
//     $meta = get_post_meta( $post_id, $key, true );
//     return ( !empty($meta) ? $meta : $default );
// }

// function radio_player_get_setting( $key, $default = '' )
// {
//     $settings = radio_player_get_settings();
//     if ( isset( $settings[$key] ) ) {
//         return $settings[$key];
//     }
//     return $default;
// }

// function radio_player_get_user_ip()
// {
    
//     if ( !empty($_SERVER['HTTP_CLIENT_IP']) ) {
//         //ip from share internet
//         $ip = $_SERVER['HTTP_CLIENT_IP'];
//     } elseif ( !empty($_SERVER['HTTP_X_FORWARDED_FOR']) ) {
//         //ip pass from proxy
//         $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
//     } else {
//         $ip = $_SERVER['REMOTE_ADDR'];
//     }
    
//     return $ip;
// }

// function radio_player_get_settings()
// {
//     $default_settings = [
//         'httpPlayer'            => false,
//         'volume'                => 80,
//         'customPopupSize'       => false,
//         'popupWidth'            => 420,
//         'popupHeight'           => 330,
//         'proxyURL'              => '',
//         'excludeAll'            => false,
//         'excludePages'          => [],
//         'stickyStyle'           => 'fullwidth',
//         'excludeExceptPages'    => [],
//         'enableStats'           => false,
//         'ads_report_recipients' => get_bloginfo( 'admin_email' ),
//     ];
//     $saved_settings = get_option( 'radio_player_settings', [] );
//     $settings = array_merge( $default_settings, $saved_settings );
//     return ( !empty($settings) ? $settings : $default_settings );
// }

// function radio_player_get_play_count( $post_id )
// {
//     global  $wpdb ;
//     $sql = $wpdb->prepare( "SELECT SUM(`count`)  FROM {$wpdb->prefix}radio_player_statistics WHERE player_id = %d;", $post_id );
//     $count = $wpdb->get_var( $sql );
//     return intval( $count );
// }

// function radio_player_get_players( $id = false )
// {
//     global  $wpdb ;
//     $table_name = $wpdb->prefix . 'radio_player_players';
    
//     if ( $id ) {
//         $player = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table_name} WHERE id = %d", $id ), ARRAY_A );
//         return radio_player_get_formatted_player( $player );
//     }
    
//     $players = $wpdb->get_results( "SELECT * FROM {$table_name}", ARRAY_A );
//     $formatted_players = [];
//     if ( !empty($players) ) {
//         foreach ( $players as $player ) {
//             $formatted_players[] = radio_player_get_formatted_player( $player );
//         }
//     }
//     if ( $id === 0 ) {
//         return $formatted_players[0];
//     }
//     return $formatted_players;
// }

// function radio_player_get_formatted_player( $player )
// {
//     if ( empty($player) ) {
//         return [];
//     }
//     $player['id'] = intval( $player['id'] );
//     $player['status'] = filter_var( $player['status'], FILTER_VALIDATE_BOOLEAN );
//     $player['config'] = unserialize( $player['config'] );
//     $player['count'] = radio_player_get_play_count( $player['id'] );
//     $player['locations'] = ( !empty($player['locations']) ? array_values( maybe_unserialize( $player['locations'] ) ) : [] );
//     return $player;
// }

// function rp_sanitize_array( $array )
// {
//     foreach ( $array as $key => &$value ) {
        
//         if ( is_array( $value ) ) {
//             $value = rp_sanitize_array( $value );
//         } else {
            
//             if ( in_array( $value, [ 'true', 'false' ] ) ) {
//                 $value = filter_var( $value, FILTER_VALIDATE_BOOLEAN );
//             } elseif ( is_numeric( $value ) ) {
                
//                 if ( strpos( $value, '.' ) !== false ) {
//                     $value = floatval( $value );
//                 } elseif ( filter_var( $value, FILTER_VALIDATE_INT ) !== false && $value <= PHP_INT_MAX ) {
//                     $value = intval( $value );
//                 } else {
//                     // Keep large integers or non-integer values as string
//                     $value = $value;
//                 }
            
//             } else {
//                 $value = wp_kses_post( $value );
//             }
        
//         }
    
//     }
//     return $array;
// }
