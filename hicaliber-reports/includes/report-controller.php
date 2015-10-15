<?php

class ReportController {

    public function generate_report($output = true)
    {
    	switch ($this->type) 
    	{
    		case 'renewal_memberships':
    			{
    				$this->data = $this->fetch_members('renewal');
	    			break;
    			}
    		case 'course_registrations':
    			{
    				$this->data = $this->fetch_course_registrations();
	    			break;
    			}
    		case 'course_payments':
    			{
    				$this->data = $this->fetch_course_payments();
	    			break;
    			}
    		default: //new members
    			{
    				$this->data = $this->fetch_members('new');
	    			break;
    			}
    	}

    	if(!is_array($this->data))
    	{
    		$this->error = $this->data;
    	}

    	if($output)
    	{
	    	require ( plugin_dir_path ( __FILE__ ) . DIRECTORY_SEPARATOR . 'report-view.php' );
    	}
    }

    private function fetch_members($type)
    {
    	$field = '';
    	$date_label = '';
    	if($type === 'new')
    	{
    		$field = 'registered_date';
	    	$date_label = 'Date Registered';
    	}
    	else if($type === 'renewal')
    	{
    		$field = 'renewal_date';
	    	$date_label = 'Renewal Date';
    	}
    	else
    	{
    		error_log('Invalid fetch_member parameter.');
    		return;
    	}

    	$columns = array(
    		'firstname' => 'First Name',
    		'lastname' => 'Last Name',
    		'concat(address1, " ", address2, " ", suburb, " ", state, " ", postcode)' => 'Address',
    		'phone' => 'Phone',
    		'date_format('.$field.',"%d/%m/%Y")' => $date_label
    	);

    	return $this->fetch_date_range_data('user_details', $columns, $field);
    }

    private function fetch_course_registrations()
    {
    	$join = array(
    		'gm_course_registration' => array('gm_course', 'id', 'courseid')
    	);

    	$columns = array(
    		'course_name' => 'Course Name',
    		'firstname' => 'First Name',
    		'lastname' => 'Last Name',
    		'concat(address_unit, " ", address_number, " ", city_suburb, " ", state, " ", postcode)' => 'Address',
    		'email' => 'Email',
    		'date_format(enrollment_date,"%d/%m/%Y")' => 'Date Registered'
    	);

    	return $this->fetch_date_range_data($join, $columns, 'enrollment_date');
    
    }

    private function fetch_course_payments()
    {
    	global $wpdb;

    	$jointable = array(
    		'gm_course_payment_log' => array('gm_course_registration', 'id', 'registrationid'),
    		'gm_course_registration' => array('gm_course', 'id', 'courseid')
    	);

    	$columns = array(
    		$wpdb->prefix . 'gm_course.course_name' => 'Course Name',
    		$wpdb->prefix . 'gm_course_payment_log.request' => 'Request',
    		$wpdb->prefix . 'gm_course_payment_log.response' => 'Transaction Status',
    		'\'tm\'' => 'Transaction Message',
    		'\'ta\'' => 'Transaction Amount',
    		'date_format('.$wpdb->prefix.'gm_course_payment_log.log_date,"%d/%m/%Y")' => 'Payment Date',
    	);

    	$data = $this->fetch_date_range_data($jointable, $columns, $wpdb->prefix.'gm_course_payment_log.log_date');

    	if(!is_array($data))
    	{
    		return $data;
    	}

    	$custom_result = [];

    	foreach ($data['result'] as $res) 
    	{
    		$trans_arr = explode('=>',$res['response']);
    		$res['response'] = explode('[', trim($trans_arr[1]))[0];
    		$res['tm'] = explode('[', trim($trans_arr[9]))[0];
    		$res['ta'] = explode('[', trim($trans_arr[8]))[0];

            $res['tm'] = '<span class="trans-message">' . $res['tm'] . '</span>';
    		$res['ta'] = '$' . ((float)$res['ta']) / 100;
    		$res['response'] = (trim($res['response']) === 'True') ? 'Success' : 'Failed';

    		$custom_result[] = $res;
    	}

    	$data['result'] = $custom_result;

    	return $data;
    }

    private function fetch_date_range_data($tablename, $columns, $datefield, $where = null)
    {
    	global $wpdb;

        $month = isset($this->month) ? $this->month : '';
        $year = isset($this->year) ? $this->year : '';

    	if(empty($tablename) || empty($datefield) || empty($columns))
    	{
    		return "Table name, date field and columns are required";
    	}

    	if(!empty($month) && !in_array($month, array_keys($this->months)))
    	{
    		return "Invalid month";
    	}

    	if(!in_array((int)$year, $this->years, true))
    	{
    		$year = date('Y');
    	}

    	if(is_array($tablename))
    	{
    		$tablename = $this->build_table_join($tablename, $wpdb->prefix);
    		$cols = array_keys($columns);
    	}
    	else
    	{
    		$tablename = $wpdb->prefix . $tablename;
    	}

    	$query_cols = implode(',', array_keys($columns));

    	$where = ( empty($where) ? "WHERE" : "WHERE $where AND" ) . " year($datefield) = $year";

    	if(!empty($month))
    	{
    		$where .= " AND month($datefield) = $month";
    	}

        $total = $wpdb->get_var("SELECT count(*) FROM $tablename $where ORDER BY $datefield $order_type");

        $paginate = empty($this->export) ? "LIMIT $this->limit OFFSET $this->offset" : "";

    	$results = $wpdb->get_results( 
			"SELECT $query_cols FROM $tablename $where ORDER BY $datefield $order_type $paginate", ARRAY_A
		);


		$data['headers'] = array_values($columns);
		$data['result'] = $results;
		$data['count'] = $total;

		//print_r($wpdb->last_query);

		return $data;
    }

    private function build_table_join($joins, $prefix)
    {
    	$joinstr = '';

    	foreach ($joins as $key => $value) 
    	{
    		$key = $prefix . $key;
    		if(empty($joinstr))
    		{
    			$joinstr = "$key ";
    		}

    		$jointype = sizeof($value) > 4 ? $value[3] : 'LEFT JOIN';
    		$joinstr .= "$jointype {$prefix}$value[0] ON {$prefix}{$value[0]}.{$value[1]}={$key}.{$value[2]} ";
    	}

    	return $joinstr;
    }

    public function export()
    {
        $export = $_GET['export'];

        $self  = new ReportController;
        $self->export = $export;
        if($export === 'xls')
        {
            $self->generate_report(false);
            $self->generate_xls();
            exit;
        }
        else if($export === 'pdf')
        {
            include( plugin_dir_path ( __FILE__ ) . implode(DIRECTORY_SEPARATOR, array('..', 'lib', 'MPDF57', 'mpdf.php')) );
            ob_start();
            $self->generate_report();
            $html = ob_get_contents();
            ob_end_clean();

            $mpdf=new \mPDF();
            $mpdf->useOnlyCoreFonts = true;
            $mpdf->WriteHTML($html);
            $mpdf->Output();
            exit;
        }
        else if($export === 'print')
        {
            $self->generate_report();
            exit;
        }
        
    }

    private function generate_xls()
    {
        if(empty($this->data['count']) || $this->data['count'] == 0) return;

        include( plugin_dir_path ( __FILE__ ) . implode(DIRECTORY_SEPARATOR, array('..', 'lib', 'PHPExcel180', 'Classes', 'PHPExcel.php')) );
        include( plugin_dir_path ( __FILE__ ) . implode(DIRECTORY_SEPARATOR, array('..', 'lib', 'PHPExcel180', 'Classes', 'PHPExcel', 'IOFactory.php')) );
        $objPHPExcel = new \PHPExcel();  
        $objPHPExcel->setActiveSheetIndex(0);  
        $rowCount = 1;  

        $headers = array_values($this->data['headers']);
        $column = 'A';
        for ($i = 0; $i < sizeof($headers); $i++)  
        {
            $objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, $headers[$i]);
            $column++;
        }

        $rowCount = 2;  
        foreach($this->data['result'] as $res)  
        {  
            $column = 'A';
            foreach($res as $k=>$v)  
            {  
                if(!isset($v))  
                    $value = NULL;  
                elseif ($v != "")  
                    $value = strip_tags($v);  
                else  
                    $value = "";  

                $objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, $value);
                $column++;
            }  
            $rowCount++;
        } 

        $column--;
        $sheet = $objPHPExcel->getActiveSheet();
        $sheet->getStyle("A1:{$column}1")->getFont()->setBold(true);
        $cellIterator = $sheet->getRowIterator()->current()->getCellIterator();
        $cellIterator->setIterateOnlyExistingCells(true);

        foreach ($cellIterator as $cell) {
            $sheet->getColumnDimension($cell->getColumn())->setAutoSize(true);
        }

        $filename = $this->type . '_' . time();
        header('Content-Type: application/vnd.ms-excel'); 
        header('Content-Disposition: attachment;filename="'.$filename.'.xls"'); 
        header('Cache-Control: max-age=0'); 
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007'); 
        $objWriter->save('php://output');
    }

    public function __construct()
	{
		$thisYear = (int) date('Y');
    	$this->years = range($thisYear, $thisYear - 5);

    	$this->report_types = array(
    		"new_memberships" => 'New Memberships',
			"renewal_memberships" => 'Membership Renewals',
			"course_registrations" => 'Course Registrations',
			"course_payments" => 'Course Payments'
    	);

		$this->months = array(
	 		"" => 'Select Month',
	 		"1" => 'January',
	 		"2" => 'February',
	 		"3" => 'March',
	 		"4" => 'April',
	 		"5" => 'May',
	 		"6" => 'June',
	 		"7" => 'July',
	 		"8" => 'August',
	 		"9" => 'September',
	 		"10" => 'October',
	 		"11" => 'November',
	 		"12" => 'December'
	 	);

	 	$this->type  = $_GET['type'];
    	$this->month_param = $_GET['month'];
    	$this->year_param  = $_GET['year'];
    	$this->order = $_GET['order'];
        $this->limit = 10;
        $this->offset = (isset($_GET['paginate']) ? ($_GET['paginate'] - 1) : 0) * $this->limit;
        $this->paginate = $_GET['paginate'];
    	$this->data  = null;
	}
}
