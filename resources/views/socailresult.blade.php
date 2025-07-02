@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">Social Detection Results</h1>
        <div class="overflow-x-auto">
            <table class="table w-full table-zebra">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Scraped Data ID</th>
                        <th>Data</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($results as $result)
                        <tr>
                            <td>{{ $result->id }}</td>
                            <td>{{ $result->scraped_data_id }}</td>
                            <td class="text-sm">
                                @php
                                    $data = is_array($result->data) ? $result->data : json_decode($result->data, true);
                                    $output = $data['output'] ?? '';
                                    // Ubah \n jadi <br> dan escape XSS
                                    $formattedOutput = nl2br(e($output));

                                    // Cek apakah ada URL di output
                                    preg_match('/https:\/\/[^\s]+/', $output, $matches);
                                    $url = $matches[0] ?? null;
                                @endphp

                                {!! $formattedOutput !!}

                                @if($url)
                                    <div class="mt-2">
                                        <a href="{{ $url }}" class="text-blue-500 underline" target="_blank">Lihat Postingan</a>
                                    </div>
                                @endif
                            </td>
                            <td>{{ $result->created_at }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection