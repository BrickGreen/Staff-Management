<!--
	Brick Green
	Date: 05/18/15
	Description: The result will be a formatted pdf with a repeating header for
					each page with additional headers and labels from
					individual tables.
-->

	<?php

	include('mysql_table.php');

	class ConnectQuery {

		//Precondition: The sql query has correct syntax and is directed at the correct tables
		//Postcondition: The results of the query is returned in a traverable array
		public function all($mysqlquery) {
			try{

				//connect to database
				include('dbconnect.php');
				$query = $conn->prepare($mysqlquery);
				$query->execute();

				//fetch all of the data and store it in an array
				$people = $query->fetchAll(PDO::FETCH_ASSOC);
			}
			catch (PDOException $e) {
				echo "Error: $e->getMessage();";
			}
			//free resources fromt he query
			$query = null;

			//return array
			return $people;
		}
	}


	class PDF extends PDF_MySQL_Table
	{
		function Header()
		{
			//Title
			$this->SetFont('Arial','B',18);
			$this->Cell('','','Division of Infectious Diseases Contact List',1, 1, 'C', True);
			$this->ln();
			//ensure the table header is output
			parent::Header();
		}
	}

	class Table extends FPDF
	{
		//Precondition: The values in $data are greater than or equal to the
		//						number of fields in the $headings result. The $headings
		//						and $data have been entered as a call to stored proceudre.
		//Postcondition: A table with a heading, column labels, and the data was returned.
		public function CreateTable($header, $headings, $data)
		{
			//Header

			$this->Ln(); //new line between tables

			$this->SetFont('Arial', 'B', 12);
			$this->SetFillColor(192); //fill color for table header
			$this->Cell(205, 10, $header, 1, 0, 'C', true);
			// Cell(float w [, float h [, string txt [, mixed border [, int ln [, string align [, boolean fill [, mixed link]]]]]]])


			$this->Ln(); //new line for column headings
			//column headings

			$this->SetFont('Arial','B', 10); //set font for column headings
			foreach ($headings as $col) {

				$this->Cell($col[1], 10, $col[0], 1, 0, 'C');
				//Cell(float w [, float h [, string txt [, mixed border [, int ln [, string align [, boolean fill [, mixed link]]]]]]])
			}

			$this->Ln();

			//Data

			$this->SetFont('Arial', '', 8); //set font for data
			foreach ($data as $row) {

				$i = 0;
				foreach ($row as $field) {
					$this->Cell($headings[$i][1], 6, $field, 1, 0, 'C');
					$i++;

				}
				$this->Ln();
			}

		}
	}


	//column headings for the department table
	$dept_header = array(array('Name', 85), array('Phone', 60), array('Fax', 60));

	//column headings for the team tables
	$team_header = array(array('Name', 35), array('Role', 30), array('Office', 22), array('Cell', 22), array('Email', 51), array('Pager', 25), array('Intercom', 20));

	//create connection
	$query = new ConnectQuery();

	//get data
	$dept_data = $query->all('call SELECT_DEPARTMENTS()');
	$faculty_data = $query->all('call SELECT_FACULTY()');
	$fellow_data = $query->all('call SELECT_FELLOWS()');
	$staff_data = $query->all('call SELECT_STAFF()');
	$research_data = $query->all('call SELECT_RESEARCH()');
	$ghro_data = $query->all('call SELECT_GHRO()');
	$lab_data = $query->all('call SELECT_LABORATORY()');
	$vaccine_research_data = $query->all('call SELECT_VACCINE_REFUGEE()');
	$hiv_data = $query->all('call SELECT_HIV()');
	$kycare_data = $query->all('call SELECT_KYCARE()');

	//create table object
	$pdf = new Table('P', 'mm', 'Letter');

	$pdf->SetMargins(5, 5, 5);
	$pdf->AddPage();

	//create individual tables in the pdf
	$pdf->CreateTable('Main Numbers', $dept_header, $dept_data);
	$pdf->CreateTable('ID Faculty', $team_header, $faculty_data);
	$pdf->CreateTable('ID Fellows', $team_header, $fellow_data);
	$pdf->CreateTable('Office Staff', $team_header, $staff_data);
	$pdf->CreateTable('Research Staff', $team_header, $research_data);
	$pdf->CreateTable('Global Health Research Organization', $team_header, $ghro_data);
	$pdf->CreateTable('ID Laboratory/Biorepository Laboratory', $team_header, $lab_data);
	$pdf->CreateTable('Vaccine and Refugee Program', $team_header, $vaccine_research_data);
	$pdf->CreateTable('HIV Clinic', $team_header, $hiv_data);
	$pdf->CreateTable('KY Care Coordination Program', $team_header, $kycare_data);


	ob_end_clean(); //cleans cache so the data is retrieved from the database after each browser refresh
	$pdf->Output(); //output the object

	?>
