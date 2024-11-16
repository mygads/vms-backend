const puppeteer = require('puppeteer');
const axios = require('axios');
const path = require('path');

(async () => {
  const visitorId = process.argv[2];
  if (!visitorId) {
    console.error('Visitor ID is required');
    process.exit(1);
  }

  try {
    const response = await axios.get(`http://127.0.0.1:8000/api/visitor/${visitorId}`);
    if (!response.data.success) {
      throw new Error('Visitor not found');
    }

    const visitor = response.data.data;

    const browser = await puppeteer.launch();
    const page = await browser.newPage();
    await page.setContent(`
      <h1>Visitor Details</h1>
      <p><strong>ID:</strong> ${visitor.visitor_id}</p>
      <p><strong>Name:</strong> ${visitor.visitor_name}</p>
      <p><strong>From:</strong> ${visitor.visitor_from}</p>
      <p><strong>Host:</strong> ${visitor.visitor_host}</p>
      <p><strong>Needs:</strong> ${visitor.visitor_needs}</p>
      <p><strong>Check-in:</strong> ${visitor.visitor_checkin}</p>
    `);

    // Save the PDF to the public/storage directory
    const pdfPath = path.join(__dirname, '../public/storage', `visitor_${visitorId}.pdf`);
    await page.pdf({ path: pdfPath, format: 'A4' });

    await browser.close();

    console.log('Print generated successfully');
  } catch (error) {
    console.error('Error generating print:', error.message);
  }
})();
