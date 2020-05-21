<?php
$question = [
	'hardware'=>[
		'Hardware Requirements:'=>[
			'16 GB Ram & Intel Core i7 Processor',
			'2 GB Ram & Dual Core Processor',
			'8 GB Ram & Intel Core i5 Processor',
			'4 GB Ram & Intel Core i3 Processor'
		]
	],
	'software'=>[
		'Software Requirements:'=>[
			'Tensorflow','Kile','Planner','Android Studio','Eclipse IDE','Wireshark','Nmap','Netkit','Cisco Packet Tracer','Oracle Virtual Box','MASM','GCC','JDK','VMWare','GDB','Net Beans','DOSEMU','Python','PDB','MySql','PostgreSQL','CrypTool','Nessus','Docker','NGINX','Nagios','ELK Stack','Rapidminer','Weka','MongoDB','Pentaho','Hadoop','HDP','R'
		]
	],
	'capacity'=>[
		'Capacity:'=>[30,24,60]
	],
	'date'=>[
		'Select Date:'=>['<input type="date" name="date" class="date form-control" id="date" min="'.date('Y-m-d', strtotime('+1 day')).'">']
	],
	'labs'=>[
		'Select Lab:'=>[

		]
	],
	'slot'=>[
		'Select Slot:'=>['09:10-10:05','10:05-11:00','11:00-11:55','11:55-12:25','12:25-01:20','01:20-02:15','02:15-02:35','02:35-03:30', '03:30-04:25', '04:25-05:20']
	],
	'input'=>[
		'Booking Purpose:'=>[
			'<input type="text" class="bookedfor form-control" id="bookedfor">
			<br>
			<button id="bookingpurpose" class="btn-primary">Save</button>
			'
		]
	]
];
echo json_encode($question);

?>