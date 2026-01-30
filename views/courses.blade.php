@extends('layout')

@section('content')
<h1>{{ $title ?? 'Welcome' }}</h1>

<div class="container">
    @if($courses)
        <h2>Available Courses</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Course Name</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($courses as $course)
                    <tr>
                        <td>{{ $course['id'] }}</td>
                        <td>{{ $course['course_name'] }}</td>
                        <td>{{ $course['description'] }}</td>
                        <td>
                            <a href="edit.php?id={{ $course['id'] }}">Edit</a>
                            <a href="delete.php?id={{ $course['id'] }}">Delete</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">No courses found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @else
        <p>No courses available.</p>
    @endif
</div>

@endsection
