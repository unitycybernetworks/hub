let companies = [];
let contacts = [];

async function loadData() {
  const [companiesRes, contactsRes] = await Promise.all([
    fetch('data/companies.json'),
    fetch('data/contacts.json')
  ]);

  companies = await companiesRes.json();
  contacts = await contactsRes.json();
  renderData();
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
      </li>`
    ).join("");

    container.innerHTML += `
      <div class="result-item">
        <h2>${company.name}</h2>
        <p><strong>Phone:</strong> ${company.phone}</p>
        <p><strong>Website:</strong> <a href="${company.website}" target="_blank">${company.website}</a></p>
        <p><strong>Address:</strong> ${company.address}</p>
        <h4>Contacts:</h4>
        <ul>${contactHTML}</ul>
      </div>`;
  });
}

function renderTags(tags = []) {
  return tags.map(tag => `<span class="tag">${tag}</span>`).join(" ");
}

document.getElementById("searchInput").addEventListener("input", e => {
  renderData(e.target.value.toLowerCase());
});

loadData();
