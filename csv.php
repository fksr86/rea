<?php
function csv($validator) {
    if ($validator == 'csv') {

        #Open the file and creates a multimensional array
        if (($handle = fopen("http://www.mapasdigitais.org/rea/wp-content/themes/rea/teste.csv", "r")) !== FALSE) {

            # Set the parent multidimensional array key to 0.
            $nn = 0;
            while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {
                if ($nn != 0 && $nn != 1 ) {
            # Count the total keys in the row.
                    $c = count($data);
            # Populate the multidimensional array.
                    for ($x=0;$x<$c;$x++)
                    {
                        $csvarray[$nn][$x] = $data[$x];
                    }
                }
                $nn++;
            }
        # Close the File.
            fclose($handle);
        }

        #Gets CSV file (file.csv) from the server and parses it into an array.
        #The file should have four columns: post_id, post_name (ignored), lat, lon, map number (from the wp_postmeta setting)
        foreach( $csvarray as $row ) {   
            $post_name = "<!--:en-->".$row[0]."<!--:--><!--:es-->".$row[1]."<!--:--><!--:pt-->".$row[2]."<!--:-->";
            $url = $row[3];

            $categories_words = explode(', ', $row[4]);
            $categories = array();
            foreach( $categories_words as $category_word) {
                switch ($category_word) {
                    case "Agregador":
                    $categories[] = '4';
                    break;

                    case "Dinâmico":
                    $categories[] = '2';
                    break;

                    case "Repositório":
                    $categories[] = '3';
                    break;
                }
            }

            $organization['en'] = $row[5];
            $organization['es'] = $row[6];
            $organization['pt'] = $row[7];

            $page = get_page_by_title( $row[8] );
            $country = $page->ID;

            $geocode_latitude = $row[9];
            $geocode_longitude = $row[10];
            $geocode_address = $row[11];

            $city['en'] = $row[12];
            $city['es'] = $row[13];
            $city['pt'] = $row[14];

            $interface_languages = explode(', ', $row[15]);
            $interface_languages = array_map('strtolower', $interface_languages);

            $resource_languages = explode(', ', $row[16]);
            $resource_languages = array_map('strtolower', $resource_languages);

            $site_license = explode(', ', $row[17]);
            $site_license = array_map('strtolower', $site_license);
            foreach ($site_license as $key=>$value) {
                $site_license[$key] = str_replace(" ","_",$value);
                $site_license[$key] = str_replace("-","_",$value);
            }

            $resource_licenses = explode(', ', $row[18]);
            $resource_licenses = array_map('strtolower', $resource_licenses);
            foreach ($resource_licenses as $key=>$value) {
                $resource_licenses[$key] = str_replace(" ","_",$value);
                $resource_licenses[$key] = str_replace("-","_",$value);
            }

            $organizations['en'] = $row[19];
            $organizations['es'] = $row[20];
            $organizations['pt'] = $row[21];

            $collections = $row[22];
            $contact = $row[23];
            $site_accessibility = $row[24];

            $resource_types = explode(', ', $row[25]);
            $resource_types = array_map('strtolower', $resource_types);
            foreach ($resource_types as $key=>$value) {
                $resource_types[$key] = str_replace(" ","_",$value);
            }

            $academic_level = explode(', ', $row[26]);
            foreach ($academic_level as $key=>$value) {
                $academic_level[$key] = filter_var($value, FILTER_SANITIZE_NUMBER_INT);
                preg_match('!\d+!', $value, $academic_level[$key]);
                $academic_level[$key] = $academic_level[$key][0][0];
            }

            $subject_area = explode(', ', $row[27]);
            $subject_area = array_map('strtolower', $subject_area);
            foreach ($subject_area as $key=>$value) {
                $subject_area[$key] = str_replace(" ","_",$value);
            }

            $funders['en'] = $row[28];
            $funders['es'] = $row[29];
            $funders['pt'] = $row[30];

            $site_time_markers = $row[31];
            $output_interfaces = $row[32];

            $input_by_users = explode(', ', $row[33]);
            $input_by_users = array_map('strtolower', $input_by_users);
            foreach ($input_by_users as $key=>$value) {
                $input_by_users[$key] = str_replace(" ","_",$value);
            }

            $my_post = array(
                'post_title'    => $post_name,
                'post_status'   => 'pending',
                'post_category' => $categories
                );
            $post_id = wp_insert_post( $my_post );
            add_post_meta( $post_id, 'artwork_url', $url );
            add_post_meta( $post_id, 'artwork_organization', $organization );
            add_post_meta( $country, '_artworks', $post_id );
            add_post_meta( $post_id, 'geocode_latitude', $geocode_latitude );
            add_post_meta( $post_id, 'geocode_longitude', $geocode_longitude );
            add_post_meta( $post_id, 'geocode_address', $geocode_address );
            add_post_meta( $post_id, 'artwork_city', $city );
            add_post_meta( $post_id, 'artwork_interface_languages', $interface_languages );
            add_post_meta( $post_id, 'artwork_resource_languages', $resource_languages );
            add_post_meta( $post_id, 'artwork_site_license', $site_license );
            add_post_meta( $post_id, 'artwork_resource_license', $resource_licenses );
            add_post_meta( $post_id, 'artwork_organizations', $organizations );
            add_post_meta( $post_id, 'artwork_collections', $collections );
            add_post_meta( $post_id, 'artwork_contact', $contact );
            add_post_meta( $post_id, 'artwork_site_accessibility', $site_accessibility );
            add_post_meta( $post_id, 'artwork_resource_types', $resource_types );
            add_post_meta( $post_id, 'artwork_academic_level', $academic_level );
            add_post_meta( $post_id, 'artwork_subject_areas', $subject_area );
            add_post_meta( $post_id, 'artwork_funders', $funders );
            add_post_meta( $post_id, 'artwork_site_time_markers', $site_time_markers );
            add_post_meta( $post_id, 'artwork_output_interfaces', $output_interfaces );
            add_post_meta( $post_id, 'artwork_input_by_users', $input_by_users );
        }
    }
}
?>