const functions = require('firebase-functions');
const { WebhookClient } = require('dialogflow-fulfillment');
const fetch = require('node-fetch');

exports.dialogflowFirebaseFulfillment = functions.https.onRequest(async (request, response) => {
  const agent = new WebhookClient({ request, response });

  async function nextEventHandler(agent) {
    try {
      const res = await fetch('https://mi-linux.wlv.ac.uk/~2337117/next_event.php');

      const text = await res.text(); // read as raw text for now
      console.log('Raw response:', text);

      // Try to parse manually
      const event = JSON.parse(text);

      const date = new Date(event.start_time);
      const options = { 
        weekday: 'long', 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric', 
        hour: 'numeric', 
        minute: '2-digit', 
        hour12: true 
      };
      const formatted = date.toLocaleString('en-UK', options);

      agent.add(`The next event is "${event.title}", and it starts on ${formatted}.`);
    } catch (error) {
      console.error('FETCH ERROR:', error);
      agent.add(`Sorry, something went wrong. Error: ${error.message}`);
    }
  }

  const intentMap = new Map();
  intentMap.set('GetNextEvent', nextEventHandler);
  agent.handleRequest(intentMap);
});
