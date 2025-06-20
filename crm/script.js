let companies = JSON.parse(localStorage.getItem("companies") || "[]");
let contacts = JSON.parse(localStorage.getItem("contacts") || "[]");

function saveToStorage() {
  localStorage.setItem("companies", JSON.stringify(companies));
  localStorage.setItem("contacts", JSON.stringify(contacts));
}

function renderData(filter = "") {
  const container = document.getElementById("results");
  container.innerHTML = "";

  const filteredCompanies = companies.filter(c =>
    c.name.toLowerCase().includes(filter)
  );

  filteredCompanies.forEach(company => {
    const companyContacts = contacts.filter(c => c.companyId === company.id);
    const contactHTML = companyContacts.map(contact =>
      `<li>
        ${contact.name} (${contact.position}) - ${contact.email}
        ${renderTags(contact.tags)}
        <button onclick="editContact(${contact.id})">Edit</button>
        <button onclick="deleteContact(${contact.id})">Delete</button>
      </li>`
    ).join("");

    container.innerHTML += `
      <div class="result-item">
        <h2>${company.name}</h2>
        <p><strong>Phone:</strong> ${company.phone}</p>
        <p><strong>Website:</strong> <a href="${company.website}" target="_blank">${company.website}</a></p>
        <p><strong>Address:</strong> ${company.address}</p>
        <button onclick="editCompany(${company.id})">Edit</button>
        <button onclick="deleteCompany(${company.id})">Delete</button>
        <h4>Contacts:</h4>
        <ul>${contactHTML}</ul>
      </div>`;
  });

  updateContactCompanyOptions();
}

function renderTags(tags) {
  return tags.map(t => `<span class="tag">${t}</span>`).join(" ");
}

function updateContactCompanyOptions() {
  const select = document.getElementById("contactCompany");
  select.innerHTML = companies.map(c => `<option value="${c.id}">${c.name}</option>`).join("");
}

function showCompanyForm(company = {}) {
  document.getElementById("companyId").value = company.id || "";
  document.getElementById("companyName").value = company.name || "";
  document.getElementById("companyPhone").value = company.phone || "";
  document.getElementById("companyWebsite").value = company.website || "";
  document.getElementById("companyAddress").value = company.address || "";
  document.getElementById("companyForm").style.display = "block";
}

function showContactForm(contact = {}) {
  document.getElementById("contactId").value = contact.id || "";
  document.getElementById("contactName").value = contact.name || "";
  document.getElementById("contactEmail").value = contact.email || "";
  document.getElementById("contactPosition").value = contact.position || "";
  document.getElementById("contactCompany").value = contact.companyId || "";
  document.getElementById("tagVIP").checked = contact.tags?.includes("VIP") || false;
  document.getElementById("tagMain").checked = contact.tags?.includes("Main Contact") || false;
  document.getElementById("contactForm").style.display = "block";
}

function hideForm(id) {
  document.getElementById(id).style.display = "none";
}

function saveCompany() {
  const id = document.getElementById("companyId").value;
  const company = {
    id: id ? Number(id) : Date.now(),
    name: document.getElementById("companyName").value,
    phone: document.getElementById("companyPhone").value,
    website: document.getElementById("companyWebsite").value,
    address: document.getElementById("companyAddress").value
  };

  const index = companies.findIndex(c => c.id === company.id);
  if (index >= 0) companies[index] = company;
  else companies.push(company);

  saveToStorage();
  hideForm("companyForm");
  renderData();
}

function saveContact() {
  const id = document.getElementById("contactId").value;
  const contact = {
    id: id ? Number(id) : Date.now(),
    companyId: Number(document.getElementById("contactCompany").value),
    name: document.getElementById("contactName").value,
    email: document.getElementById("contactEmail").value,
    position: document.getElementById("contactPosition").value,
    tags: []
  };

  if (document.getElementById("tagVIP").checked) contact.tags.push("VIP");
  if (document.getElementById("tagMain").checked) contact.tags.push("Main Contact");

  const index = contacts.findIndex(c => c.id === contact.id);
  if (index >= 0) contacts[index] = contact;
  else contacts.push(contact);

  saveToStorage();
  hideForm("contactForm");
  renderData();
}

function editCompany(id) {
  const company = companies.find(c => c.id === id);
  showCompanyForm(company);
}

function editContact(id) {
  const contact = contacts.find(c => c.id === id);
  showContactForm(contact);
}

function deleteCompany(id) {
  companies = companies.filter(c => c.id !== id);
  contacts = contacts.filter(ct => ct.companyId !== id);
  saveToStorage();
  renderData();
}

function deleteContact(id) {
  contacts = contacts.filter(c => c.id !== id);
  saveToStorage();
  renderData();
}

document.getElementById("searchInput").addEventListener("input", e => {
  renderData(e.target.value.toLowerCase());
});

renderData();
