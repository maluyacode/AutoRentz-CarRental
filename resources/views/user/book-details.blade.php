@if ($bookInfo['start_date'] || $bookInfo['end_date'])
    <strong>Start Date:</strong>
    <?php
    $date_string = $bookInfo['start_date'];
    $dateStr = new DateTime($date_string);
    $formatedDate = date_format($dateStr, 'M d, Y');
    echo $formatedDate;
    ?> |
    <strong>End Date: </strong>
    <?php
    $date_string = $bookInfo['end_date'];
    $dateStr = new DateTime($date_string);
    $formatedDate = date_format($dateStr, 'M d, Y');
    echo $formatedDate;
    ?>
    |
    <?php
    $datetime1 = date_create($bookInfo['start_date']);
    $datetime2 = date_create($bookInfo['end_date']);
    $diff = date_diff($datetime1, $datetime2);
    $count = (int) $diff->format('%a');
    $display = $count + 1;
    echo '<strong>' . $display . ' </strong> day(s) |';
    ?>
@else
    <strong>Start Date:</strong> <em> Please Specify</em> |
    <strong>End Date: </strong> <em> Please Specify</em> |
    <strong>Day(s): </strong> <em> Undetermined</em> |
@endif
<strong>
    @if ($bookInfo['driver_id'] == 1)
        With Driver
    @elseif ($bookInfo['driver_id'] == 0)
        Self Drive
    @endif
</strong>
