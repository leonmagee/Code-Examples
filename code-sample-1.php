<?php

/*
* Helper Functions - Sim Track Manager
*/


class STM_Functions {
    

    // End Session

    static function destroy_session_and_data() {

        $_SESSION = array();

        if ( session_id() != "" || isset( $_COOKIE[session_name()] ) )

        setcookie( session_name(), '', time() - 2592000, '/' );

        session_destroy();
    }


    // Add '$' and 2 Decimal Places

    static function num_out( $val ) {

        return "$" . number_format( $val, 2 );
    }


    // Form Input Handling

    static function get_post( $string ) {

        $string = $_POST[ $string ];

        if ( get_magic_quotes_gpc() ) { $string = stripslashes( $string ); }

        return mysql_real_escape_string( htmlentities( $string ) );

    }

    static function get_get( $string ) {

        $string = $_GET[ $string ];

        if ( get_magic_quotes_gpc() ) { $string = stripslashes( $string ); }

        return mysql_real_escape_string( htmlentities( $string ) );

    }


    // Month/Day/Year => Year-Month-Day

    static function fix_date( $var ) {

        $date_array = explode('/',$var);

        $month = $date_array[0];

        $day = $date_array[1];

        $year = $date_array[2];

        $var_ex = explode(' ',$year);

        $year_final = $var_ex[0];

        if ( count( $date_array ) == 3 ) {

            $new_date = $year_final . "-" . $month . "-" .$day;

            return $new_date;

        } else {

            return $month;
        }
    }

    static function remove_underline( $var ) {

        $pattern = '/\_/';

        $replacement = ' ';

        return preg_replace( $pattern, $replacement, $var );
    }

    static function th_real_name( $var ) {

        switch( $var ) {

        case 'SIM_Num':

            return 'SIM Number';
            break;

        case 'Value':

            return 'Plan Value';
            break;

        case 'Act_Date':

            return 'Activation Date';
            break;

        case 'Mob_Num':

            return 'Mobile Number';
            break;

        default:

            return $var;
            break;
        }	
    }

    static function fix_date_time($var) {

        $var_ex = explode(' ',$var);

        $var_final = fix_date($var_ex[0]);

        return $var_final;
    }

    static function calc_month_resid( $value, $percent ) {

        $residual_payment = ( $value * ( $percent/100 ) );

        return $residual_payment;	
    }


    static function calc_spiff_payment( $value_s, $percent_s ) {

        $spiff_payment = ( $value_s * ( $percent_s/100 ) );

        return $spiff_payment;	
    }

    static function remove_dollar( $value ) {

        $pattern = '/\$/';

        $replacement = '';

        return preg_replace( $pattern, $replacement, $value );
    }
    
    static function month_to_month_name( $month ) {
        
        switch ( $month ) {
            case 1 : $month_name = 'January';
                break;
            case 2 : $month_name = 'February';
                break;
            case 3 : $month_name = 'March';
                break;
            case 4 : $month_name = 'April';
                break;
            case 5 : $month_name = 'May';
                break;
            case 6 : $month_name = 'June';
                break;
            case 7 : $month_name = 'July';
                break;
            case 8 : $month_name = 'August';
                break;
            case 9 : $month_name = 'September';
                break;
            case 10 : $month_name = 'October';
                break;
            case 11 : $month_name = 'November';
                break;
            case 12 : $month_name = 'December';
                break;
        }
        
        return $month_name;
    }
    
    
        static function month_to_month_name_exp( $month_year ) {
        
        $month_exp = explode( ' - ', $month_year );
        
        $month = $month_exp[0];
            
        switch ( $month ) {
            case 1 : $month_name = 'January';
                break;
            case 2 : $month_name = 'February';
                break;
            case 3 : $month_name = 'March';
                break;
            case 4 : $month_name = 'April';
                break;
            case 5 : $month_name = 'May';
                break;
            case 6 : $month_name = 'June';
                break;
            case 7 : $month_name = 'July';
                break;
            case 8 : $month_name = 'August';
                break;
            case 9 : $month_name = 'September';
                break;
            case 10 : $month_name = 'October';
                break;
            case 11 : $month_name = 'November';
                break;
            case 12 : $month_name = 'December';
                break;
        }
        
        return $month_name . " - " . $month_exp[1];
    }
    

    static function table_exists( $tableName ) {

        if( mysql_num_rows( mysql_query( "SHOW TABLES LIKE '$tableName'") ) ) {

            return TRUE;

        } else {

            return FALSE;
        }
    }

    static function add_column_if_not_exist( $column, $table, $column_attr = "FLOAT NULL" ) {
        
        global $db_my_server;

        $exists = false;

        $columns = mysql_query( "show columns from $table", $db_my_server );

        while( $c = mysql_fetch_assoc( $columns ) ) {

            if( $c['Field'] == $column ) {

                $exists = true;
                break;
            }
        }

        if( !$exists ) {

            mysql_query( "ALTER TABLE $table ADD $column  $column_attr", $db_my_server );
        }
    }
    
    
    static function export_csv( $array ) {

        $fp = fopen('csv_downloads/file.csv', 'w');

        foreach ( $array as $fields ) {

            fputcsv( $fp, $fields );
        }

        fclose($fp);

    }
    
    
    static function spiff_res_total( $agent_id, $table_name ) {
        
        
        // Query Table Data
        

        $query_table = new Query_STM( "SELECT Spiff_True FROM table_data WHERE table_name = '$table_name'" );

        $row_table = mysql_fetch_row( $query_table->result );

        $res_or_spiff = $row_table[0];
        

        // Set Residual Percent
        
        if ( $res_or_spiff == '0' ) { // is residual
            
            $res = new Query_STM( "SELECT `Percent` FROM `agent_residual` WHERE `AgentID` = '$agent_id' AND 
                                   `Table` = '$table_name'" );

            $row = mysql_fetch_row( $res->result );
            
            //die( $row[0] );

            $agent_percent = $row[0];
        
            //$agent_percent = '100';
        }
        
        
        // Query Plan Data
    
        $query_plans = new Query_STM( "SELECT `value`, `spiff`, `table_name`, `plan_id` FROM `plan_list`" );

        for ( $n = 0; $n < $query_plans->rows; ++$n ) {

            $row_plans = mysql_fetch_row( $query_plans->result );

            $plan_value[$n] = $row_plans[0];

            $plan_spiff[$n] = $row_plans[1];

            $plan_table_name[$n] = $row_plans[2];
            
            $plan_id[$n] = $row_plans[3];
        }
    
        
        // Query SIMs
        
        $query = new Query_STM( "SELECT $table_name.Value FROM $table_name, sims_all WHERE 
                             $table_name.SIM_Num = sims_all.SIM_Num AND sims_all.AgentID = $agent_id" );


        for ( $s = 0; $s < $query->rows; ++$s ) {

            $row_sims = mysql_fetch_row( $query->result );

            $SIM_Value[$s] = $row_sims[0];


            if ( '0' == $res_or_spiff ) {

                $res_spiff_val[$s] = STM_Functions::calc_month_resid( $SIM_Value[$s], $agent_percent );

                $res_spiff_total += $res_spiff_val[$s];
                
                $res_or_spiff_array[] = $res_spiff_val[$s];

            }


            elseif ( '1' == $res_or_spiff ) {

                for ( $p = 0; $p < $query_plans->rows; ++$p ) {

                    if ( ( $SIM_Value[$s] == $plan_value[$p] ) && ( $plan_table_name[$p] == $table_name ) ) {
                        
                         /*
                         *  query the new `agent_plans` table to find a Payment value if `PlanID` and `AgentID`
                         *  match. if the value is not zero, assign the value... 
                         */

                        $agent_plans = new Query_STM( "SELECT `Payment` FROM `agent_plans` WHERE `PlanID` = 
                                                      '$plan_id[$p]' AND `AgentID` = '$agent_id'" );

                        $row = mysql_fetch_row( $agent_plans->result );

                        if ( $row[0] ) {

                            $act_val_var = $row[0];

                        } else {

                            $act_val_var = $plan_spiff[$p];
                        }

                        break;

                    }

                    else {

                        $act_val_var = 0;
                    }	
                }
                
                $res_spiff_val[$s] = $act_val_var;
                
                
                $res_spiff_total += $res_spiff_val[$s]; 
                
                $res_or_spiff_array[] = $res_spiff_val[$s];
            }

            else {

                $res_spiff_val[$s] = 0;
            }       
        }
    
        
        $total_value = number_format( $res_spiff_total, 2 );

        $number_sims = $query->rows;
        
        $total_value_obj = new Total_Value( $total_value, $number_sims, $res_or_spiff, $res_or_spiff_array );

        return $total_value_obj;
  
        
    }
    
 

}

?>
