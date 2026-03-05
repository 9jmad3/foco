<div class="w-full min-h-screen flex flex-col items-center justify-start sm:justify-center px-0 sm:px-6 py-0 sm:py-6">
    <div class="w-full sm:w-auto pt-10 sm:pt-0 flex justify-center">
        {{ $logo }}
    </div>

    <div class="w-full min-h-[calc(100vh-96px)] sm:min-h-0 sm:max-w-md mt-6 sm:mt-6
                px-6 py-8 sm:px-6 sm:py-6
                bg-white
                sm:shadow-md sm:overflow-hidden sm:rounded-lg">
        {{ $slot }}
    </div>
</div>
