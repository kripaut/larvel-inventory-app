// Dummy Data for Inventory System
// Products, Companies, Categories, etc.

window.dummyProducts = [];
window.dummyCompanies = [];

window.loadCategoriesData = async function() {
  try {
    const response = await fetch('assets/js/categories.json');
    if (!response.ok) {
      throw new Error('Failed to load categories.json: ' + response.status);
    }
    window.dummyCompanies = await response.json();
  } catch (error) {
    console.error(error);
    window.dummyCompanies = [];
  }
  return window.dummyCompanies;
};

window.loadProductData = async function() {
  try {
    const response = await fetch('assets/js/products.json');
    if (!response.ok) {
      throw new Error('Failed to load products.json: ' + response.status);
    }
    window.dummyProducts = await response.json();
  } catch (error) {
    console.error(error);
    window.dummyProducts = [];
  }
  return window.dummyProducts;
};

window.getCompanyNames = function() {
  return window.dummyCompanies.map(company => company.name);
};

window.getAllCategories = function() {
  const categories = [];
  window.dummyCompanies.forEach(company => {
    company.categories.forEach(category => {
      if (!categories.includes(category.name)) {
        categories.push(category.name);
      }
    });
  });
  return categories;
};

window.getCompanyCategories = function(companyName) {
  const company = window.dummyCompanies.find(c => c.name === companyName);
  return company ? company.categories.map(category => category.name) : [];
};

window.getCompanySubcategories = function(companyName, categoryName) {
  const company = window.dummyCompanies.find(c => c.name === companyName);
  if (!company) return [];
  const category = company.categories.find(cat => cat.name === categoryName);
  return category ? category.subcategories : [];
};
