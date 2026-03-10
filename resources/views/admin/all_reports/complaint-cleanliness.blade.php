<?php
$user_id = app('request')->input('user_id');
$selected_branch_id = null;

if (!empty($user_id)) {
    $selected_branch_id = DB::table('userbranches')
        ->where('user_id', $user_id)
        ->value('branch_id');
}

$branch_location = app('request')->input('branch_location');
$branches = \App\Branch::where('status', '1');
if (!empty($branch_location)) {
    $branches->where('branch_location', $branch_location);
}
$branches = $branches->orderBy('id', 'desc')->get();											
                ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complaint Cleanliness Report Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background-image: linear-gradient(to top, #f38800, #f39300, #f29e00, #f1a900, #f0b400, #efbc02, #eec506, #edcd0e, #edd60e, #ecde11, #eab516, #f0841c);
        }
    </style>
</head>

<body>
    <div class="container p-4 mt-3">
        <div class="d-flex justify-content-between">
            <div class="">
                <h2 class="fs-5 mb-4">Complaint Cleanliness Report</h2>
            </div>
            <div class="">
                <a href="{{ route('app-view-complaint', ['user_id' => $user_id]) }}" class="btn btn-dark"
                    style="font-size:12px;">History</a>
            </div>
        </div>
        <form action="{{ route('complaint-cleanliness.submit') }}" method="POST" class="card p-2"
            enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label for="users-list-status">Branch</label>
                <input type="hidden" name="user_id" value="{{ $user_id }}">
                <fieldset class="form-group">
                    <select class="form-control branch_id" name="branch_id">
                        <option value="">Select Any</option>
                        @foreach($branches as $value)
                            <option value="{{ $value->id }}" @if(old('branch_id', $selected_branch_id) == $value->id) selected
                            @endif>
                                {{ $value->name }}
                            </option>
                        @endforeach
                    </select>
                </fieldset>
            </div>
            <div class="mb-3">
                <label for="complaint" class="form-label fw-bold">Write Your Complaint</label>
                <textarea class="form-control" id="complaint" name="complaint" rows="4"
                    placeholder="write complaint..."></textarea>
                @if($errors->has('complaint'))
                    <span class="text-danger">{{ $errors->first('complaint') }} </span>
                @endif
            </div>

            <div class="mb-3">
                <label for="media" class="form-label fw-bold">Upload Image or Video</label>
                <input class="form-control" type="file" id="media" name="media" accept="image/*,video/*">
                @if($errors->has('media'))
                    <span class="text-danger">{{ $errors->first('media') }} </span>
                @endif
            </div>

            <div class="text-center">
                <button type="submit" id="submit_btn" class="btn btn-dark px-4">Submit Complaint</button>
            </div>
        </form>
    </div>
    @if(session('success'))
    <script>
        if (performance.navigation.type !== 2) { // Not a back/forward navigation
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '{{ session('success') }}',
                confirmButtonColor: '#28a745'
            });
        }
    </script>
@endif


    @if(session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: '{{ session('error') }}',
                confirmButtonColor: '#dc3545'
            });
        </script>
    @endif
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.querySelector('form').addEventListener('submit', function () {
            const submitBtn = document.getElementById('submit_btn');
            submitBtn.innerHTML = 'Processing...';
            submitBtn.disabled = true;
        });
    </script>
</body>

</html>