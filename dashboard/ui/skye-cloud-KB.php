<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: welcome.php");
    exit;
}

// Prevent caching of this page
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
if (isset($_SERVER['HTTP_USER_AGENT']) && (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false)) {
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
}

// ... rest of your secure page content
?>

<!DOCTYPE html>
<html lang="en">
<head>
 <meta charset="UTF-8">
 <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <title>Knowledge Base | Skye Cloud Hub</title>
 <link rel="icon" type="image/jpg" href="skyecloudlogo.jpg">
 <style>
 * {
   margin: 0;
   box-sizing: border-box;
   font-family: Arial, sans-serif;
 }
 body {
   background-color: skyblue;
   color: white;
 }
 #chatbot {
   position: fixed;
   top: 0;
   left: 0;
   width: 100vw;
   height: 100vh;
   background-color: skyblue;
   display: flex;
   flex-direction: column;
   justify-content: space-between;
   padding: 20px;
 }
  .footer {
   background: darkblue;
   padding: 10px 20px;
   text-align: center;
   font-weight: bold;
 }
 #chat-box {
   flex-grow: 1;
   overflow-y: auto;
   padding: 10px;
   border: 3px solid skyblue;
 }
 .msg {
   background: darkblue;
   color: white;
   padding: 10px;
   margin-bottom: 10px;
   border-radius: 5px;
 }
 .opt {
   display: block;
   background: darkblue;
   padding: 10px;
   margin-top: 5px;
   cursor: pointer;
   border-radius: 5px;
   text-align: left;
   width: 100%;
 }

 #init {
   position: fixed;
   bottom: 20px;
   right: 20px;
   padding: 10px 15px;
   background: skyblue;
   color: white;
   border: none;
   cursor: pointer;
   display: none; /* Hide START CHAT button as it's no longer needed for login */
 }
 #searchContainer {
   display: none;
   margin-bottom: 10px;
 }
 #searchInput {
   width: 100%;
   padding: 10px;
   border-radius: 5px;
   border: 3px solid darkblue;
   background: white;
   color: black;
 }
 .dropdown {
   background: darkblue;
   max-height: 200px;
   overflow-y: auto;
   position: relative;
   width: calc(100% - 20px);
 }
 .dropdown span {
   display: block;
   padding: 10px;
   cursor: pointer;
   border-bottom: 3px solid black;
 }

 #selectedItem {
   display: none;
   font-size: 16px;
   font-weight: bold;
   margin-bottom: 10px;
   text-align: left;
   color: white;
   background: darkblue;
   padding: 10px;
   border-radius: 5px;
 }
 .header {
   background-color: darkblue;
   padding: 10px 20px;
   display: flex;
   justify-content: space-between;
   align-items: center;
   color: white;
 }

 .header-title {
   color: white;
   font-size: 24px;
   flex-grow: 1; /* Allows the title to take up available space */
   text-align: center; /* Center the text */
 }

 #logoutButton, #refreshButton {
   padding: 8px 15px;
   background-color: blue;
   color: white;
   border: none;
   cursor: pointer;
   border-radius: 5px;
   display:block; /* Ensure buttons are always visible */
 }
 
 </style>
</head>
<body>
 <button id="init">START CHAT</button>

 <div id="chatbot">
       <div class="header">
        <button id="refreshButton">REFRESH</button>
        <span class="header-title">Skye Cloud Knowledge Base</span>
        <button id="logoutButton">LOGOUT</button>
</div>
   <div id="chat-box"></div>
   <div class="footer">Powered by Skye Cloud's Infrastructure Team</div>
 </div>
 <script>

 var guideData = {
   "Cyber Security": { options: { "Best practices for downloading and sharing personal Data": "https://skyecloudltd.sharepoint.com/:w:/r/sites/CyberSecurity/_layouts/15/Doc.aspx?sourcedoc=%7BACBC0D13-8CAD-4253-A5CC-58D84EBE13C2%7D&file=Best%20practices%20for%20Downloading%20and%20Sharing%20Personal%20data.docx&action=default&mobileredirect=true",
   "Blacklisting & Whitelisting Emails via Microsoft Defender": "https://skyecloudltd.sharepoint.com/:w:/r/sites/CyberSecurity/_layouts/15/Doc.aspx?sourcedoc=%7B3496994C-A2B1-492E-8875-AF9D42F96F61%7D&file=Blacklisting%20%26%20Whitelisting%20emails%20via%20Microsoft%20Defender.docx&action=default&mobileredirect=true",
   "Client Password Policy": "https://skyecloudltd.sharepoint.com/:w:/r/sites/CyberSecurity/_layouts/15/Doc.aspx?sourcedoc=%7B744C7320-BA9F-4468-8B0A-FE02A8FF7B86%7D&file=Client%20Password%20Policy.docx&action=default&mobileredirect=true",
   "Dos and Don'ts for password security": "https://skyecloudltd.sharepoint.com/:w:/r/sites/CyberSecurity/_layouts/15/Doc.aspx?sourcedoc=%7BC8309753-B1B5-40D0-B870-FCF9D8395B54%7D&file=Dos%20and%20Don%E2%80%99ts%20for%20password%20security.docx&action=default&mobileredirect=true",
   "Managing User Accounts in Duo for IT Engineers": "https://skyecloudltd.sharepoint.com/:w:/r/sites/CyberSecurity/_layouts/15/Doc.aspx?sourcedoc=%7BA7BC8A3A-16B2-4429-8224-7A96B2C63045%7D&file=DUO%20-%20Managing%20User%20Accounts%20in%20Duo%20for%20IT%20Engineers.docx&action=default&mobileredirect=true",
   "ESET Detection Guide": "https://skyecloudltd.sharepoint.com/:w:/r/sites/CyberSecurity/_layouts/15/Doc.aspx?sourcedoc=%7BB0695C25-EA97-42AB-ABF6-00BE0416254C%7D&file=ESET%20Detection%20Guide.docx&action=default&mobileredirect=true",
   "Guide to Setting Up Duo Mobile for Secure Access to Remote Desktop Services": "https://skyecloudltd.sharepoint.com/:w:/r/sites/CyberSecurity/_layouts/15/Doc.aspx?sourcedoc=%7B992F7067-0C5F-4DBB-AFA7-E2131E4E8AB6%7D&file=Guide%20to%20Setting%20Up%20Duo%20Mobile%20.docx&action=default&mobileredirect=true",
   "How to Enrol Your Phone in to Microsoft Intune": "https://skyecloudltd.sharepoint.com/:w:/r/sites/CyberSecurity/_layouts/15/Doc.aspx?sourcedoc=%7BF496773B-519C-4E6F-BE20-25E40C575C78%7D&file=How%20to%20Enroll%20Your%20Phone%20in%20to%20Microsoft%20Intune.docx&action=default&mobileredirect=true",
   "Mail Assure Report Guide": "https://skyecloudltd.sharepoint.com/:w:/r/sites/CyberSecurity/_layouts/15/Doc.aspx?sourcedoc=%7B0BBD791E-C10A-48DA-82B4-44A93432C75E%7D&file=Mail%20Assure%20Report%20Guide.docx&action=default&mobileredirect=true",
   "Microsoft Defender – Malware Detection process": "https://skyecloudltd.sharepoint.com/:w:/r/sites/CyberSecurity/_layouts/15/Doc.aspx?sourcedoc=%7BE0EBDC4C-A3B9-4958-A171-B87DB11A6E9F%7D&file=Microsoft%20Defender%20Malware%20detection%20alert%20process.docx&action=default&mobileredirect=true",
   "Investigating OpsGenie Alerts for Newcross": "https://skyecloudltd.sharepoint.com/:w:/r/sites/CyberSecurity/_layouts/15/Doc.aspx?sourcedoc=%7BC4D298D2-4A3D-447C-BBD1-2A5F592808E1%7D&file=OPSGenie%20Account%20Takeover%20Alert%20Guide.docx&action=default&mobileredirect=true",
   "Risky Sign in/Risky User Guide": "https://skyecloudltd.sharepoint.com/:w:/r/sites/CyberSecurity/_layouts/15/Doc.aspx?sourcedoc=%7B522B3F2F-540B-41E9-AB59-5C0E478D8CF0%7D&file=Risky%20Sign%20in%20-%20Risky%20User%20Alerts%20Guide.docx&action=default&mobileredirect=true",
   "Setting Up Multi-Factor Authentication for Your Microsoft 365 Sign-In ": "https://skyecloudltd.sharepoint.com/sites/CyberSecurity/Shared%20Documents/Forms/AllItems.aspx?id=%2Fsites%2FCyberSecurity%2FShared%20Documents%2FGuides%2FSetting%20Up%20Multi%2DFactor%20Authentication%20for%20Your%20Microsoft%20365%20Sign%2DIn%2Epdf&viewid=2b317236%2D3253%2D4875%2Db164%2De3fd8d2d0b18&parent=%2Fsites%2FCyberSecurity%2FShared%20Documents%2FGuides",
   "Sharing Files Externally: Dos and Don'ts": "https://skyecloudltd.sharepoint.com/:w:/r/sites/CyberSecurity/_layouts/15/Doc.aspx?sourcedoc=%7BA68DE6E4-E852-4359-B124-98984831A634%7D&file=Sharing_Files_Externally_Dos_and_Donts.docx&action=default&mobileredirect=true",
   "Upgrading to Windows 11": "https://skyecloudltd.sharepoint.com/:w:/r/sites/CyberSecurity/_layouts/15/Doc.aspx?sourcedoc=%7B94C7825E-6BD4-4C9F-89E1-05498030B6FE%7D&file=Upgrading%20to%20Windows%2011.docx&action=default&mobileredirect=true",
} },
 };

 var processesData = {
    "New Starter Processes": { options: {
        "Onboarding Checklists": "https://skyecloudltd.sharepoint.com/sites/onboarding-checklist/",
        
    }},
    
 };


 var chatData = {
   greeting: ["Welcome to Skye Cloud Bot👋", "Choose an option below to get started"],
   options: [ "Guides", "Processes"], // MODIFIED: Removed "Useful Links"
   guides: { title: "Please select a guide category", options: Object.keys(guideData) },
   processes: { title: "Please select a process category", options: Object.keys(processesData) },
 };

 window.onload = function () {
   initChat(); // Initialize chat directly
 };

 // Event listener for LOGOUT button
 document.getElementById("logoutButton").addEventListener("click", function() {
   // Clear the history state
   window.history.pushState(null, "", "logged-out");
   window.location.replace('logout.php'); // Use replace to prevent back navigation
 });

 // Event listener for REFRESH button
 document.getElementById("refreshButton").addEventListener("click", function() {
   location.reload(); // Reload the current page
 });

 function initChat() {
   var chatBox = document.getElementById("chat-box");
   chatBox.innerHTML = "";
   chatData.greeting.forEach(text => {
     var msg = document.createElement("p");
     msg.innerHTML = text;
     msg.setAttribute("class", "msg");
     chatBox.appendChild(msg);
   });
   showOptions(chatData.options);
 }
 function showOptions(options) {
   var chatBox = document.getElementById("chat-box");
   options.forEach(option => {
     var opt = document.createElement("span");
     opt.innerHTML = option;
     opt.setAttribute("class", "opt");
     opt.addEventListener("click", function () {
       chatBox.innerHTML += `<p class="msg">You selected: <strong>${option}</strong></p>`;
       chatBox.querySelectorAll(".opt").forEach(el => el.remove());
       if (option.toLowerCase() === "guides") {
         displaySearch(guideData, "guide");
       } else if (option.toLowerCase() === "processes") { // Added for Processes
         displaySearch(processesData, "process");
       }
     });
     chatBox.appendChild(opt);
   });
 }
 function displaySearch(data, type) {
   var chatBox = document.getElementById("chat-box");
   chatBox.innerHTML += `<div id="searchContainer"><input type="text" id="searchInput" placeholder="Search for a ${type}..." onkeyup="filterItems('${type}')"><div id="dropdown" class="dropdown"></div></div>`;
   chatBox.innerHTML += `<div id="selectedItem"></div>`;
   document.getElementById("searchContainer").style.display = "block";
   filterItems(type);
 }
 function filterItems(type) {
   var input = document.getElementById("searchInput").value.toLowerCase();
   var dropdown = document.getElementById("dropdown");
   var data;
   if (type === "guide") {
     data = guideData;
   } else if (type === "process") { // Added for Processes
     data = processesData;
   }

   dropdown.innerHTML = "";
   Object.keys(data).forEach(item => {
     if (item.toLowerCase().includes(input)) {
       var option = document.createElement("span");
       option.innerHTML = item;
       option.addEventListener("click", function () {
         document.getElementById("searchContainer").style.display = "none";
         document.getElementById("selectedItem").style.display = "block";
         document.getElementById("selectedItem").innerHTML = `You selected: <strong>${item}</strong>`;
         displaySubcategories(data[item].options);
       });
       dropdown.appendChild(option);
     }
   });
 }
 function displaySubcategories(subcategories) {
   var chatBox = document.getElementById("chat-box");
   // Clear previous options when displaying new set of subcategories
   chatBox.querySelectorAll(".opt").forEach(el => el.remove());
   document.getElementById("searchContainer") && (document.getElementById("searchContainer").style.display = "none");
   document.getElementById("selectedItem") && (document.getElementById("selectedItem").style.display = "none");

   // Check if subcategories is a nested object (like usefulLinksData)
   if (Object.values(subcategories).every(val => typeof val === 'object' && val !== null && 'options' in val)) {
     Object.keys(subcategories).forEach(category => {
       var categoryHeader = document.createElement("p");
       categoryHeader.innerHTML = `<strong>${category}</strong>`;
       categoryHeader.setAttribute("class", "msg");
       chatBox.appendChild(categoryHeader);

       Object.keys(subcategories[category].options).forEach(subOption => {
         var link = subcategories[category].options[subOption];
         var container = document.createElement("div");
         container.setAttribute("class", "opt");

         var label = document.createElement("span");
         label.textContent = subOption;
         label.style.display = "block";
         label.style.marginBottom = "5px";

         var button = document.createElement("button");
         button.textContent = "Open Link";
         button.style.padding = "8px 12px";
         button.style.backgroundColor = "#007bff";
         button.style.color = "white";
         button.style.border = "none";
         button.style.borderRadius = "10px";
         button.style.cursor = "pointer";
         button.onclick = function () {
           window.open(link, "_blank");
         };

         container.appendChild(label);
         container.appendChild(button);
         chatBox.appendChild(container);
       });
     });
   } else { // Handle flat list of subcategories (like company/guide/process details)
     Object.keys(subcategories).forEach(subOption => {
       var link = subcategories[subOption];
       var container = document.createElement("div");
       container.setAttribute("class", "opt");

       var label = document.createElement("span");
       label.textContent = subOption;
       label.style.display = "block";
       label.style.marginBottom = "5px";

       var button = document.createElement("button");
       button.textContent = "Open Link";
       button.style.padding = "8px 12px";
       button.style.backgroundColor = "#007bff";
       button.style.color = "white";
       button.style.border = "none";
       button.style.borderRadius = "10px";
       button.style.cursor = "pointer";
       button.onclick = function () {
         window.open(link, "_blank");
       };

       container.appendChild(label);
       container.appendChild(button);
       chatBox.appendChild(container);
     });
   }
 }
 </script>
</body>
</html>