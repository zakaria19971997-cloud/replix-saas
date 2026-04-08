process.removeAllListeners('warning');

import WAZIPER from "./waziper/waziper.js";

WAZIPER.app.get("/instance", async (req, res) => {
  const access_token = req.query.access_token;
  const instance_id = req.query.instance_id;
  await WAZIPER.instance(access_token, instance_id, false, res, async (client) => {
    await WAZIPER.get_info(instance_id, res);
  });
});

WAZIPER.app.get("/get_qrcode", async (req, res) => {
  const access_token = req.query.access_token;
  const instance_id = req.query.instance_id;
  await WAZIPER.instance(access_token, instance_id, true, res, async (client) => {
    await WAZIPER.get_qrcode(instance_id, res);
  });
});

WAZIPER.app.get("/get_groups", async (req, res) => {
  const access_token = req.query.access_token;
  const instance_id = req.query.instance_id;
  await WAZIPER.instance(access_token, instance_id, false, res, async (client) => {
    await WAZIPER.get_groups(instance_id, res);
  });
});

WAZIPER.app.get("/logout", async (req, res) => {
  const access_token = req.query.access_token;
  const instance_id = req.query.instance_id;
  WAZIPER.logout(instance_id, res);
});

WAZIPER.app.post("/send_message", async (req, res) => {
  const access_token = req.query.access_token;
  const instance_id = req.query.instance_id;
  await WAZIPER.instance(access_token, instance_id, false, res, async (client) => {
    WAZIPER.send_message(instance_id, access_token, req, res);
  });
});

WAZIPER.app.get("/", async (req, res) => {
  return res.json({ status: "success", message: "Welcome to WAZIPER" });
});

WAZIPER.server.listen(8000, () => {
  console.log("✅ WAZIPER IS LIVE ON PORT 8000");
});
