<div id="search" class="bg-neutral-50 dark:bg-gray-800 p-5 mb-5 rounded shadow">
    <form action="{{ route('search.index') }}" method="get" class="m-0">
        <div class="flex items-center">
            <input type="text" name="query" required minlength="3" class="w-full dark:bg-gray-900 dark:text-white p-4 border border-amber-500 dark:border-gray-700  dark:focus:bg-gray-900" placeholder="Find Recipes..."
                   value="{{ request('query') }}">
            <button type="submit"
                    class="bg-amber-500 hover:bg-amber-600 border border-amber-500 text-white py-4 px-6">Search
            </button>
        </div>
    </form>
</div>
