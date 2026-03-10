<table class="table data-list-view dataTable">
    <thead class="Tt_not_found">
        <tr>
            <th colspan="12"><b>Faculty Name: <?= $get_faculty_value->faculty_name ?? '' ?></b></th>
        </tr>
    </thead>
    <thead>
        <tr>
            <th>From Time</th>
            <th>To Time</th>
            <th>Date</th>
            <th>Branch Name</th>
            <th>Studio</th>
            <th>Batch Name</th>
            <th>Subject Name</th>
            <th>Assistant Name</th>
            <th>Schedule Time</th>
            <th>Spent Time</th>
            <th>Topic</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($get_faculty_timetable as $key => $row): ?>
            <?php
                $schedule_duration = '00 : 00 Hours';
                $duration = '00 : 00 Hours';

                $from_time = new DateTime($row->from_time);
                $to_time = new DateTime($row->to_time);
                $schedule_interval = $from_time->diff($to_time);
                $schedule_duration = $schedule_interval->format('%H : %I Hours');
                $total_base_schedule->add($schedule_interval);

                if ($row->is_cancel != 1) {
                    $start = new DateTime($row->start_classes_start_time);
                    $end = new DateTime($row->start_classes_end_time);
                    $spent_interval = $start->diff($end);
                    $duration = $spent_interval->format('%H : %I Hours');
                    $base_time->add($spent_interval);
                } else {
                    $duration = 'Cancelled';
                    $total_cancel_class++;
                    $total_base_cancel->add($schedule_interval);
                }
            ?>
            <tr>
                <td timetable-id="<?= $row->id ?>"><?= date("h:i A", strtotime($row->from_time)) ?></td>
                <td><?= date("h:i A", strtotime($row->to_time)) ?></td>
                <td><?= $row->cdate ?></td>
                <td>
                    <?= $row->branches_name ?>
                    <?php
                        if (!empty($row->branches_id)) {
                            $ch_list = DB::table('users')
                                ->leftJoin('userbranches', 'users.id', '=', 'userbranches.user_id')
                                ->leftJoin('userdetails', 'users.id', '=', 'userdetails.user_id')
                                ->select('users.name as user_name', 'users.mobile as mobile')
                                ->where('userbranches.branch_id', $row->branches_id)
                                ->where('userdetails.degination', 'CENTER HEAD')
                                ->get();

                            if ($ch_list->count() > 0) {
                                $ch_output = collect($ch_list)->map(function ($ch) {
                                    return "{$ch->user_name} ({$ch->mobile})";
                                })->implode(', ');
                                echo "<br><b>CH.-</b> $ch_output";
                            }
                        }
                    ?>
                </td>
                <td><?= $row->studios_name ?></td>
                <td>
                    <?php
                        $batch_names = '';
                        $batch_query = DB::table('timetables')
                            ->select('batch.name as b_name', 'batch.erp_course_id')
                            ->leftJoin('batch', 'batch.id', '=', 'timetables.batch_id')
                            ->where('timetables.is_deleted', '0');

                        if ($row->time_table_parent_id == 0) {
                            $batch_query->where('timetables.time_table_parent_id', $row->id);
                            $batch_names .= $row->batch_name;
                        } else {
                            $batch_query->whereRaw("(timetables.id = {$row->time_table_parent_id} OR timetables.time_table_parent_id = {$row->time_table_parent_id})");
                        }

                        $batch_list = $batch_query->get();
                        foreach ($batch_list as $b) {
                            $batch_names .= ', ' . $b->b_name . '-' . $b->erp_course_id;
                        }

                        echo $batch_names;
                    ?>
                </td>
                <td><?= $row->subject_name ?></td>
                <td>
                    <?= $row->assistant_name ?> (<?= $row->assistant_mobile ?>)
                </td>
                <td><?= $schedule_duration ?></td>
                <td class="spd_time<?= $key2 . $key ?>"><?= $duration ?></td>
                <td><?= $row->topic_name ?></td>
                <td>
                    <a href="javascript:void(0);" data-toggle="modal" data-id="<?= $row->id ?>"
                       data-spent-id="<?= $key2 . $key ?>" class="btn btn-sm btn-outline-primary get_start_class_data">
                        <span class="action-edit"><i class="feather icon-edit"></i></span>
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>

        <tr>
            <td colspan="3"><b>Total Schedule Time:</b> 
                <?php
                    $total_diff = $total->diff($total_base_schedule);
                    echo ($total_diff->days * 24 + $total_diff->h) . ':' . $total_diff->i . ' Hours';
                ?>
            </td>
            <td colspan="4"><b>Total Spent Time:</b> 
                <?php
                    $spent_diff = $total->diff($base_time);
                    echo ($spent_diff->days * 24 + $spent_diff->h) . ':' . $spent_diff->i . ' Hours';
                ?>
            </td>
            <td colspan="5">
                <b>Total Cancel Class:</b> 
                <strong style="background: red;border-radius: 50%;padding: 6px;color: #fff;">
                    <?= $total_cancel_class ?>
                </strong>
                <br>
                <b>Total Cancel Time:</b>
                <?php
                    $cancel_diff = $total->diff($total_base_cancel);
                    echo ($cancel_diff->days * 24 + $cancel_diff->h) . ':' . $cancel_diff->i . ' Hours';
                ?>
            </td>
        </tr>
    </tbody>
</table>
<p><hr/></p>
