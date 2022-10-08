<html>
<tr>
	<th>Report for the month: {{$month}}</th>
</tr>

    <tr>
	    <!--  Bold -->
	    <td><b>Name</b></td>
	    <td><b>Cnic</b></td>
	    <td><b>Phone</b></td>
	    <td><b>Position</b></td>
	    <td><b>Salary</b></td>
	    <td><b>Type</b></td>
	    <td><b>Present</b></td>
	    <td><b>Leaves</b></td>
	    <td><b>Absents</b></td>
	    <td><b>Bonus</b></td>
	    <td><b>Overtime(hrs)</b></td>
	    <td><b>Deduction</b></td>
	    <td><b>Total</b></td>
	</tr>
	
    @foreach ($employees as $employee)
    <tr>
	    <td>{{ $employee->name }}</td>
	    <td>{{ $employee->cnic }}</td>
	    <td>{{ $employee->phone }}</td>
	    <td>{{ $employee->position }}</td>
	    <td>{{ $employee->salary }}</td>
	    <td>{{ $employee->type }}</td>
	    <td>{{$employee->total_attendance}}</td>
	    <td>{{ $employee->total_leaves }}</td>
	    <td>{{$employee->active_days - $employee->total_attendance - $employee->total_leaves}}</td>
	    <td>{{ $employee->bonus }}</td>
	    <td>{{$employee->total_overtime}}</td>
	    <td>{{ $employee->deduction }}</td>
	    <td>{{ $employee->total }}</td>
    </tr>
    @endforeach

    <tr>
    	<th></th>
    	<th></th>
    	<th></th>
    	<th></th>
    	<th></th>
    	<th></th>
    	<th></th>
    	<th></th>
    	<th></th>
    	<th></th>
    	<th></th>
    	<th>Total Amount</th>
    	<th>=sum(M3:M{{count($employees)+2}})</th>
    </tr>
</html>