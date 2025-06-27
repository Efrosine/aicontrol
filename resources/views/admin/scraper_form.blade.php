@extends('layouts.app')

@section('content')
    <div class="max-w-xl mx-auto mt-10">
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title mb-4">Instagram Scraper</h2>
                <form method="POST" action="{{ route('admin.scraper.submit') }}">
                    @csrf
                    <div class="mb-4">
                        <label class="label" for="platform">Platform</label>
                        <select class="select select-bordered w-full mb-2" name="platform" id="platform" required
                            onchange="filterAccounts()">
                            <option value="" disabled selected>Select platform</option>
                            @foreach($platforms as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        <label class="label" for="accounts">Account</label>
                        <select class="select select-bordered w-full" name="accounts" id="accounts" required disabled>
                            <option value="" disabled selected>Select account</option>
                            @foreach($dummyAccounts as $acc)
                                <option value="{{ $acc->username }}" data-platform="{{ $acc->platform }}">{{ $acc->username }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="label" for="suspected_account">Suspected Account</label>
                        <input class="input input-bordered w-full" type="text" name="suspected_account"
                            id="suspected_account" required>
                    </div>
                    <div class="mb-4">
                        <label class="label" for="post_count">Post Count</label>
                        <input class="input input-bordered w-full" type="number" name="post_count" id="post_count" min="1"
                            value="10" required>
                    </div>
                    <div class="mb-4">
                        <label class="label" for="comment_count">Comment Count</label>
                        <input class="input input-bordered w-full" type="number" name="comment_count" id="comment_count"
                            min="1" value="10" required>
                    </div>
                    <div class="card-actions justify-end">
                        <button class="btn btn-primary" type="submit">Start Scraping</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        function filterAccounts() {
            const platform = document.getElementById('platform').value;
            const accountSelect = document.getElementById('accounts');
            let found = false;
            Array.from(accountSelect.options).forEach(opt => {
                if (!opt.dataset.platform) return; // skip placeholder
                if (opt.dataset.platform === platform) {
                    opt.style.display = '';
                    found = true;
                } else {
                    opt.style.display = 'none';
                }
            });
            accountSelect.disabled = !found;
            accountSelect.selectedIndex = 0;
        }
    </script>
@endsection