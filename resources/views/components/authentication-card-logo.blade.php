<a href="/" class="flex items-center space-x-3 group">
  <svg
    class="w-10 h-10 text-emerald-700 foco-brain"
    viewBox="0 0 64 64"
    fill="none"
    xmlns="http://www.w3.org/2000/svg"
    aria-label="FOCO"
  >
    <!-- Silueta cerebro (más “cerebro” y menos satélite) -->
    <path
      d="M26 12
         C18 12 12 18 12 26
         C8.5 28 7 31 7 35
         C7 41 11 45 17 46
         C18 52 23 55 29 55
         H31
         V16
         C31 13.8 29.2 12 27 12
         Z"
      stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"
      class="opacity-75"
    />
    <path
      d="M38 12
         C46 12 52 18 52 26
         C55.5 28 57 31 57 35
         C57 41 53 45 47 46
         C46 52 41 55 35 55
         H33
         V16
         C33 13.8 34.8 12 37 12
         Z"
      stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"
      class="opacity-75"
    />

    <!-- Línea central -->
    <path d="M32 16v39" stroke="currentColor" stroke-width="2" class="opacity-25"/>

    <!-- Surcos (gyri) lado izq -->
    <path d="M27 20c-5 0-8 3-8 7 0 3 2 5 5 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" class="brain-wave w1 opacity-35"/>
    <path d="M28 30c-4 0-7 2-7 6 0 3 2 5 5 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" class="brain-wave w2 opacity-35"/>
    <path d="M28 41c-3 0-6 2-6 5 0 3 2 5 5 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" class="brain-wave w3 opacity-35"/>

    <!-- Surcos lado der -->
    <path d="M37 20c5 0 8 3 8 7 0 3-2 5-5 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" class="brain-wave w4 opacity-35"/>
    <path d="M36 30c4 0 7 2 7 6 0 3-2 5-5 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" class="brain-wave w5 opacity-35"/>
    <path d="M36 41c3 0 6 2 6 5 0 3-2 5-5 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" class="brain-wave w6 opacity-35"/>

    <!-- Nodos (actividad) -->
    <circle cx="22" cy="26" r="2.2" fill="currentColor" class="brain-node n1 opacity-60"/>
    <circle cx="24" cy="40" r="2.2" fill="currentColor" class="brain-node n2 opacity-60"/>
    <circle cx="42" cy="26" r="2.2" fill="currentColor" class="brain-node n3 opacity-60"/>
    <circle cx="40" cy="42" r="2.2" fill="currentColor" class="brain-node n4 opacity-60"/>
  </svg>

  <span class="text-xl font-semibold tracking-wide text-gray-800 transition duration-300 group-hover:text-emerald-600">
    FOCO
  </span>
</a>
