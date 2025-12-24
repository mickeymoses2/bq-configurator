<?php

include 'accounts.php';


?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Modular BQ Configurator — Model 3 Bed Bungalow (Pro)</title>

  <!-- Tailwind CDN -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- Your additional stylesheet (keeps your brand styles) -->
  <link rel="stylesheet" href="../styles.css">
  <link rel="stylesheet" href="../loader.css">

  <style>
    
  </style>
</head>

<body class="bg-slate-50 text-slate-800 min-h-screen antialiased">

  <!-- =========================
       DESKTOP TOP NAV (visible lg+)
       ========================= -->
  <nav class="hidden lg:flex topbar-desktop fixed top-0 left-0 right-0 z-40
              bg-white/80 border-b border-slate-200 h-16 items-center justify-between px-8">
    <!-- Left: Logo + Title -->
    <div class="flex items-center gap-4">
      <div class="w-11 h-11 rounded-xl bg-blue-600 text-white flex items-center justify-center font-bold shadow-sm">
        LOGO
      </div>

      <div class="leading-tight">
        <div class="font-semibold text-slate-900 text-lg tracking-tight">ECON shelters</div>
        <div class="text-xs text-slate-500 -mt-0.5">Modular BQ Configurator</div>
      </div>
    </div>

    

    <!-- Right: User -->
    <div class="flex items-center gap-3">
      <svg class="w-6 h-6 text-blue-800 font-bold" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M5.121 17.804A7 7 0 0112 15a7 7 0 016.879 2.804M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
      </svg>

      <span class="text-sm text-blue-800 font-bold "><?= htmlspecialchars($_SESSION['client_name']) ?></span>
    </div>
  </nav>

  <!-- =========================
       MOBILE TOPBAR (visible < lg)
       ========================= -->
  <header class="lg:hidden fixed top-0 left-0 right-0 z-50 bg-white border-b">
    <div class="flex items-center justify-between px-4 py-3">
      <div class="flex items-center gap-3">
        <button id="mobileMenuBtn" aria-label="Open menu" class="p-2 rounded hover:bg-slate-100">
          <!-- hamburger -->
          <svg class="w-6 h-6 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
          </svg>
        </button>

        <div class="font-semibold">ECON shelters</div>
      </div>

      <div class="flex items-center gap-2">
        <svg class="w-6 h-6 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M5.121 17.804A7 7 0 0112 15a7 7 0 016.879 2.804M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
        </svg>
      </div>
    </div>
  </header>

  <!-- =========================
       BACKDROP FOR MOBILE DRAWER
       ========================= -->
  <div id="drawerBackdrop" class="hidden lg:hidden fixed inset-0 z-40 bg-black/30"></div>

  <!-- =========================
       SHORT SIDEBAR (starts below top nav on lg+)
       ========================= -->
  <aside id="sidebar"
         class="fixed z-50 left-0 lg:top-16 top-16 lg:h-[calc(100vh-64px)] h-[calc(100vh-64px)]
                bg-white border-r p-6 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 overflow-y-auto w-72">

    <!-- (LOGO was moved to top nav) -->
    <!-- Logged-in user (small) -->
    <div class="flex items-center gap-2 mb-4 p-3 bg-slate-100 rounded">
      <svg class="w-6 h-6 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M5.121 17.804A7 7 0 0112 15a7 7 0 016.879 2.804M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
      </svg>
      <span class="text-sm text-slate-700">
        Logged in as:
        <span class="font-semibold text-blue-800"><?= htmlspecialchars($_SESSION['client_name']) ?></span>
      </span>
    </div>

    <!-- Navigation -->
    <nav class="space-y-1 mb-6" aria-label="Main navigation">
      <a href="#" class="block w-full text-left px-3 py-2 rounded hover:bg-slate-50 flex items-center gap-3">
        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18" />
        </svg>
        <span class="font-medium">Home</span>
      </a>

      <a href="#" class="block w-full text-left px-3 py-2 rounded hover:bg-slate-50 flex items-center gap-3">
        <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
        </svg>
        <span class="font-medium">Progress</span>
      </a>

      <a href="#" class="block w-full text-left px-3 py-2 rounded hover:bg-slate-50 flex items-center gap-3">
        <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8h18M3 16h18" />
        </svg>
        <span class="font-medium">About Us</span>
      </a>

      <a href="#" class="block w-full text-left px-3 py-2 rounded hover:bg-slate-50 flex items-center gap-3">
        <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h12a2 2 0 012 2z" />
        </svg>
        <span class="font-medium">Contact</span>
      </a>
    </nav>

    <!-- Logout -->
    <div class="mt-6">
      <a href="logout.php" id="logoutBtn" class="w-full py-2 rounded border text-sm block text-center">Logout</a>
    </div>
  </aside>


  <!-- MAIN CONTENT -->
  <main class="ml-0 lg:ml-72 transition-all duration-300 pt-20">
    <div class="max-w-7xl mx-auto px-4 pb-32">

      <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- LEFT SECTION / MODULES -->
        <section class="lg:col-span-7 relative flex flex-col">
          <div class="max-w-3xl w-full mx-auto bg-white rounded-lg fluent-card">
            <h1 class="text-3xl font-semibold text-slate-900 mb-3 text-center">Features & Options</h1>
            <div class="accent-bar"></div>

            <div class="space-y-8">
              <div id="modulesContainer" class="space-y-4 flex-1 overflow-y-auto pb-2"></div>
            </div>
        </div>












<!-- FLOATING FOOTER (IDs restored, responsive + blue border floating style) -->
<div class="sticky bottom-4 w-full max-w-4xl mx-auto bg-white/80
backdrop-blur-lg
 backdrop-blur-md 
     border-1 border-blue-300 p-4 sm:p-6 
     flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 z-50">

  <!-- LEFT: TOTAL -->
  <div class="flex flex-col sm:flex-row items-center sm:items-center gap-3 w-full sm:w-auto">
    <span class="text-sm tracking-wide text-slate-500">TOTAL (KSh)</span>

    <!-- ID preserved -->
    <div id="parentTotal"
         class="px-4 py-2 bg-green-100 text-green-800 font-bold text-lg sm:text-xl 
                rounded-lg border border-green-300 w-full sm:w-auto text-center sm:text-left">
      4,950,000
    </div>
  </div>

  <!-- RIGHT: BUTTONS -->
  <div class="flex flex-col sm:flex-row flex-wrap gap-2 w-full sm:w-auto">

    <!-- Undo (ID preserved) -->
    <button id="undoBtnFooter"
            class="px-3 py-2 w-full sm:w-auto shrink-0 rounded-md border-2 border-blue-400 text-blue-700 
                   bg-white hover:bg-blue-50 active:scale-[0.97]
                   transition flex items-center justify-center gap-2">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M3 10v6h6M3 16l7-7 7 7"/>
      </svg>
      Undo
    </button>

  

    <!-- Reset (ID preserved) -->
    <button id="resetBtnFooter"
            class="px-3 py-2 w-full sm:w-auto shrink-0 rounded-md border-2 border-amber-400 text-amber-700 
                   bg-amber-50 hover:bg-amber-100 active:scale-[0.97]
                   transition flex items-center justify-center gap-2">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M4 4v6h6M20 20v-6h-6M12 8a4 4 0 014 4v0a4 4 0 01-4 4v0a4 4 0 01-4-4v0a4 4 0 014-4z"/>
      </svg>
      Reset
    </button>

  <!-- Submit Button -->
  <button id="suBmit"   style="display: none;"  class="px-3 py-2 w-full sm:w-auto shrink-0 rounded-md  text-amber-700 
                   bg-amber-50 hover:bg-amber-100 active:scale-[0.97]
                   transition flex items-center justify-center gap-2">
    <div id="tosubmitLoader" class="tosubmit-loader hidden"></div>
    <span id="tosubmitText" class="flex items-center gap-2">
      <svg id="tosubmitIcon" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor" style="transform: rotate(-45deg);">
        <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
      </svg>
      Submit
    </span>
  </button>

  <!-- Edit -->
<button id="editBtn"      style="display: none;"                 class="px-3 py-2 w-full sm:w-auto shrink-0 rounded-md text-amber-700 bg-amber-50  active:scale-[0.97] transition flex items-center justify-center gap-2">
    <div id="tosubmitLoader" class="tosubmit-loader hidden"></div>
    <span id="tosubmitText" class="flex items-center gap-2">
        <svg id="tosubmitIcon" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
            <path d="M7 17.01l1.59-1.59 4.38 4.38L18.42 17l1.42 1.42-7 7L7 17.01zm15.42-9.42L18.42 2.58l-1.42 1.42 4.42 4.42 1.42-1.42zM5.99 15.01l-1.42-1.42 4.38-4.38 1.42 1.42L5.99 15.01zM17.01 5.99l-1.42-1.42 4.38-4.38 1.42 1.42L17.01 5.99zM15.59 1.59L1.59 15.59 0 17.18V24h6.82l1.59-1.59L22.41 8.41l-1.42-1.42z"/>
        </svg>
        Edit
    </span>
</button>




  </div>

</div>






        </section>

        <!-- RIGHT SECTION / SUMMARY -->
        <aside class="lg:col-span-5">
          <div id="summaryCard" class="bg-white rounded-lg p-5 card sticky top-6">
            <div class="flex items-center justify-between">
              <h2 class="font-semibold text-lg">Summary (KSh)</h2>
            </div>

            <div class="mt-4 space-y-3"></div>
            <hr class="my-4">

            <div>
              <h3 class="text-sm font-medium text-slate-600"></h3>
              <div class="mt-3 space-y-2 text-sm">
                <div class="flex justify-between text-slate-600">
                  <div>Subtotal</div>
                  <div id="subtotal" class="num-anim">KSh 0</div>
                </div>

                <div class="flex justify-between text-lg font-semibold">
                  <div>Grand Total</div>
                  <div id="grandTotal" class="num-anim">KSh 0</div>
                </div>
              </div>
            </div>

           <div class="mt-8 grid grid-cols-1 gap-2">
              <button id="undoBtn" class="py-2 px-3 rounded border text-sm flex items-center gap-2">
                <!-- Undo icon -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10v6h6M3 16l7-7 7 7"/>
                </svg>
                Undo
              </button>

              <button id="redoBtn" class="py-2 px-3 rounded border text-sm flex items-center gap-2">
                <!-- Redo icon -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 10v6h-6M21 16l-7-7-7 7"/>
                </svg>
                Redo
              </button>

              <button id="resetBtn" class="py-2 px-3 rounded bg-amber-50 border text-amber-700 text-sm flex items-center gap-2">
                <!-- Reset icon -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v6h6M20 20v-6h-6M12 8a4 4 0 014 4v0a4 4 0 01-4 4v0a4 4 0 01-4-4v0a4 4 0 014-4z"/>
                </svg>
                Reset
              </button>

            <button
                  id="downloadPdfBtn"
                  class="py-2.5 px-4 rounded-lg bg-orange-600 hover:bg-orange-700 text-white text-sm font-medium 
                        flex items-center gap-2 shadow-sm hover:shadow-md transition-all active:scale-95"
                >
                  <!-- Download icon -->
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" 
                      viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5m0 0l5-5m-5 5V4"/>
                  </svg>

                  Download PDF
                </button>
<?php
echo "Status: " . $status;
?>


            </div>

          </div>
        </aside>

      </div>
    </div>
  </main>











<!-- Success Popup Overlay -->
<div id="successPopup" class="fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center z-50 hidden">
  <div class="popup-content bg-white rounded-2xl p-6 w-[90%] max-w-sm text-center shadow-xl animate-pop">

    <!-- Icon -->
    <div id="iconWrap" class="mx-auto mb-4 flex items-center justify-center w-14 h-14 rounded-full bg-green-100">
      <!-- Loader spinner -->
      <div id="popupLoader" class="w-8 h-8 border-4 border-green-600 border-t-transparent rounded-full animate-spin"></div>

      <!-- Tick icon -->
      <svg id="popupTick" xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-green-600 hidden" fill="none"
           viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M5 13l4 4L19 7" />
      </svg>
    </div>

    <h2 class="text-xl font-semibold text-gray-800 mb-1">BQ Submitted Successfully</h2>
    <p class="text-gray-600 text-sm mb-4">
      You have <span class="font-semibold">24 hours</span> to make any edits.
    </p>

    <button id="popupCloseBtn" class="w-full bg-green-600 text-white py-2 rounded-lg font-medium hover:bg-green-700 transition">
      OK
    </button>

  </div>
</div>




<div id="errorPopup" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
  <div class="bg-white rounded-lg p-6 w-80 text-center shadow-lg">
    <h2 class="text-lg font-semibold text-red-600">Save Failed</h2>
    <p class="mt-2 text-gray-700">Something went wrong. Please try again.</p>

    <button onclick="document.getElementById('errorPopup').classList.add('hidden')"
      class="mt-4 px-4 py-2 bg-red-600 text-white rounded w-full">
      Close
    </button>
  </div>
</div>















  <div id="preloader1">
    <div class="orbit1">
      <div class="circle1"></div>
      <div class="circle1"></div>
      <div class="mix1"></div>
    </div>
  </div>

  
  
  <!-- SCRIPTS -->
   <script src="../preloader.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
  <script src="script.js"></script>
  <script src="toast.js"></script>

  <script src="document.js"></script>
  
</body>
</html>
