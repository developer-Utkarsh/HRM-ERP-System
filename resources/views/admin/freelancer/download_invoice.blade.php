
<!DOCTYPE html>
<html lang='en'>
	<head>
		<meta charset='UTF-8'>
		<meta name='viewport' content='width=device-width, initial-scale=1.0'>
		<title>Invoice</title>
		<style>
			body { font-family: Arial, sans-serif; margin: 20px; }
			.bg-primary { background-color: #5e9ff3c7; border: 1px solid black; color: #000; }
			.table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
			.table th, .table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
			.text-end { text-align: right; }
			.text-center { text-align: center; }
			.fw-bold { font-weight: bold; }
			.flex-container { display: flex; justify-content: space-between; align-items: flex-start; width: 100%; }
			.flex-container > div { width: 48%; }
		</style>
	</head>
	<body>
		<div class="noPrint text-center">
			<button type="button" class="btn btn-primary mt-2" onClick="printPo()">Print</button> &nbsp;&nbsp;&nbsp;&nbsp;
			<a href="{{ route('admin.freelancer.index') }}" class="btn btn-primary mt-2">Back</a>
		</div>
		<div id='invoice'>
			<table style='width: 100%; border-collapse: collapse; margin-top: 20px;'>
				<tr>
					<td style='width: 50%; vertical-align: top;'>
						<p><strong>{{ $record->name }}</strong></p>
						<span><strong>Phone No:</strong> {{ $record->phone }}</span><br>
						<span><strong>Location:</strong> {{ $record->location }}</span>
					</td>
					<td style='width: 50%; text-align: right; vertical-align: top;'>
						<h2 style='color: #5e9ff3; margin: 0;'>INVOICE</h2>
						<table class='table'>
							<tr>
								<th class='bg-primary'>INVOICE #</th>
								<th class='bg-primary'>DATE</th>
							</tr>
							<tr>
								<td>{{ $record->id }}</td>
								<td>{{ $record->created_at }}</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>

			<div style='width:50%; margin-top: 20px;'>
				<h6 class='bg-primary' style='padding:5px;'>BILL TO</h6>
				<p> UTKARSH CLASSES &
					EDUTECH PRIVATE LIMITED
					832, Utkarsh Bhawan, Near
					Mandap Restaurant, 9th
					Chopasni Road, Jodhpur (Raj.)
					342003
				</p>
			</div>
			<table class='table'>
				<thead>
					<tr>
						<th class='bg-primary'>DESCRIPTION</th>
						<th class='bg-primary text-end'>AMOUNT</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>{{ date('d-m-Y',strtotime($record->month)) }} </br> {{ $record->description }}</td>
						<td class='text-end'>Rs. {{ $record->amount }} /-</td>
					</tr>
				</tbody>
				<tfoot>
					<tr class='fw-bold'>
						<td class='text-center'>Total</td>
						<td class='text-end'>Rs. {{ $record->amount }}/-</td>
					</tr>
				</tfoot>
			</table>
			<div>
				<h5><strong>BANK DETAILS</strong></h5>
				<p><strong>PAN NO.: </strong> {{ $record->pan }}</p>
				<p><strong>NAME:</strong> {{ $record->account_name }}</p>
				<p><strong>A/C NO.:</strong> {{ $record->account_no }}</p>
				<p><strong>IFSC:</strong> {{ $record->ifsc }}</p>
			</div>
		</div>
	</body>
</html>
	<style type="text/css">
			@media print {
			  .noPrint{
				display:none;
			  }
			}
		</style>
        <script>

		function printPo(){
			window.print(); 
			// window.close();
		};
		</script>