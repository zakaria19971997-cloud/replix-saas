/**
 * Common.js (ESM version for Waziper Baileys v7)
 * Fully merged from legacy Common.js
 * Compatible with Node 18/20/22
 */

import mysql from "mysql";
import config from "./../config.js";
import moment from "moment-timezone";

const db_connect = mysql.createPool(config.database);

const Common = {
  /* ----------------------------- DB Helpers ----------------------------- */
  db_query: async function (query, row) {
    const res = await new Promise((resolve) => {
      db_connect.query(query, (err, res) => resolve(res));
    });
    return Common.response(res, row);
  },

  db_insert: async function (table, data) {
    const res = await new Promise((resolve) => {
      db_connect.query("INSERT INTO " + table + " SET ?", data, (err, res) => resolve(res));
    });
    return res;
  },

  db_update: async function (table, data) {
    const res = await new Promise((resolve) => {
      db_connect.query("UPDATE " + table + " SET ? WHERE ?", data, (err, res) => resolve(res));
    });
    return res;
  },

  db_get: async function (table, data) {
    let query = `SELECT * FROM ${table}`;
    let where = "";
    if (data.length > 0) {
      for (let i = 0; i < data.length; i++) where += i === 0 ? " ?" : " AND ?";
    }
    if (where !== "") query += " WHERE " + where;
    const res = await new Promise((resolve) => {
      db_connect.query(query, data, (err, res) => resolve(res));
    });
    return Common.response(res, true);
  },

  db_fetch: async function (table, data) {
    let query = `SELECT * FROM ${table}`;
    let where = "";
    if (data.length > 0) {
      for (let i = 0; i < data.length; i++) where += i === 0 ? " ?" : " AND ?";
    }
    if (where !== "") query += " WHERE " + where;
    const res = await new Promise((resolve) => {
      db_connect.query(query, data, (err, res) => resolve(res));
    });
    return Common.response(res, false);
  },

  db_delete: async function (table, data) {
    let query = `DELETE FROM ${table}`;
    let where = "";
    if (data.length > 0) {
      for (let i = 0; i < data.length; i++) where += i === 0 ? " ?" : " AND ?";
    }
    if (where !== "") query += " WHERE " + where;
    const res = await new Promise((resolve) => {
      db_connect.query(query, data, (err, res) => resolve(res));
    });
    return res;
  },

  get_phone_number: async function (contact_id, phone_numbers) {
    if (!Array.isArray(phone_numbers) || phone_numbers.length === 0) {
        phone_numbers = ["__EMPTY__"];
    }

    // Internal note
    const placeholders = phone_numbers.map(() => "?").join(",");

    const sql = `
        SELECT * FROM whatsapp_phone_numbers 
        WHERE pid = ?
        AND phone NOT IN (${placeholders})
        ORDER BY id ASC
        LIMIT 1
    `;

    const params = [contact_id, ...phone_numbers];

    const res = await new Promise((resolve) => {
        db_connect.query(sql, params, (err, res) => resolve(res));
    });

    return Common.response(res, true);
  },

  get_instance: async function (instance_id) {
    const res = await new Promise((resolve) => {
      db_connect.query("SELECT * FROM whatsapp_sessions WHERE ?", [{ instance_id }], (err, res) => resolve(res));
    });
    return Common.response(res, true);
  },

  get_accounts: async function (accounts) {
    const res = await new Promise((resolve) => {
      db_connect.query(
        "SELECT count(*) as count FROM accounts WHERE id IN (" + accounts + ") AND status = 1",
        (err, res) => resolve(res)
      );
    });
    return Common.response(res, true);
  },

  db_insert_account: async function (instance_id, team_id, wa_info) {
    const data = {
      ids: Common.makeid(13),
      module: "whatsapp_unofficial_profiles",
      social_network: "whatsapp_unofficial",
      category: "profile",
      login_type: 2,
      can_post: 0,
      team_id: team_id,
      pid: Common.get_phone(wa_info.id, "wid"),
      name: wa_info.name,
      username: Common.get_phone(wa_info.id),
      token: instance_id,
      avatar: wa_info.avatar,
      url: "https://web.whatsapp.com/",
      tmp: JSON.stringify(wa_info),
      status: 1,
      changed: Common.time(),
      created: Common.time(),
    };
    const res = await new Promise((resolve) => {
      db_connect.query("INSERT INTO accounts SET ?", data, (err, res) => resolve(res));
    });
    return res;
  },

  db_update_account: async function (instance_id, team_id, wa_info, account_id) {
    const data = [
      {
        module: "whatsapp_unofficial_profiles",
        social_network: "whatsapp_unofficial",
        category: "profile",
        login_type: 2,
        pid: Common.get_phone(wa_info.id, "wid"),
        name: wa_info.name,
        username: Common.get_phone(wa_info.id),
        token: instance_id,
        avatar: wa_info.avatar,
        tmp: JSON.stringify(wa_info),
        status: 1,
        changed: Common.time(),
      },
      { id: account_id },
    ];
    const res = await new Promise((resolve) => {
      db_connect.query("UPDATE accounts SET ? WHERE ?", data, (err, res) => resolve(res));
    });
    return res;
  },

  db_insert_stats: async function (team_id) {
    const data = {
      ids: Common.makeid(13),
      team_id: team_id,
      wa_total_sent_by_month: 0,
      wa_total_sent: 0,
      wa_chatbot_count: 0,
      wa_autoresponder_count: 0,
      wa_api_count: 0,
      wa_bulk_total_count: 0,
      wa_bulk_sent_count: 0,
      wa_bulk_failed_count: 0,
      wa_time_reset: 0,
      next_update: 0,
    };
    const res = await new Promise((resolve) => {
      db_connect.query("INSERT INTO whatsapp_stats SET ?", data, (err, res) => resolve(res));
    });
    return res;
  },

  update_status_instance: async function (instance_id, info) {
    const data = [
      {
        status: 1,
        data: JSON.stringify(info),
      },
      { instance_id: instance_id },
    ];
    const res = await new Promise((resolve) => {
      db_connect.query("UPDATE whatsapp_sessions SET ? WHERE ?", data, (err, res) => resolve(res));
    });
    return res;
  },

  /* ----------------------------- Utility helpers ----------------------------- */
  makeid(length) {
    const chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
    let result = "";
    for (let i = 0; i < length; i++) result += chars.charAt(Math.floor(Math.random() * chars.length));
    return result.toLowerCase();
  },

  time() {
    return Math.round(new Date().getTime() / 1000);
  },

  sleep: async function (ms) {
    return new Promise((resolve) => setTimeout(resolve, ms));
  },

  randomIntFromInterval(min, max) {
    return Math.floor(Math.random() * (max - min + 1) + min);
  },

  get_phone(id, type) {
    switch (type) {
      case "wid":
        const parts = id.split(":");
        if (parts.length === 2) {
          const id1 = parts[0];
          const id2 = parts[1].split("@");
          id = id1 + "@" + id2[1];
        }
        break;
      default:
        const basic = id.split(":");
        if (basic.length === 2) id = basic[0];
        else {
          id = id.split("@")[0];
        }
    }
    return id;
  },

  get_avatar(text, color) {
    if (!text) return false;
    const colors = ["E74645", "FB7756", "FACD60", "12492F", "F7A400", "58B368"];
    color = color || colors[Math.floor(Math.random() * colors.length)];
    text = text.replace(/[&=~'" ]/g, "");
    return `https://ui-avatars.com/api/?name=${encodeURI(text)}&background=${color}&color=fff&font-size=0.5&rounded=false&format=png`;
  },

  check_especials(phone) {
    return new Promise((resolve) => {
      let current_phone = phone;
      if (!phone) return resolve(phone);

      if (phone.startsWith("55")) {
        const ddd = phone.substring(2, 4);
        if (ddd >= 31 && phone.length >= 13) {
          phone = phone.substring(0, 4) + phone.substring(5);
        }
      }

      if (phone.startsWith("52") && phone.length === 12 && phone.substring(2, 3) !== "1") {
        phone = phone.substring(0, 2) + "1" + phone.substring(2);
      }

      if (phone !== current_phone) {
        const query = `UPDATE whatsapp_phone_numbers SET phone='${phone}' WHERE phone=${current_phone}`;
        db_connect.query(query, (err) => resolve(phone));
      } else {
        resolve(phone);
      }
    });
  },

  roundMinutes(date) {
    date.setHours(date.getHours() + 1);
    date.setMinutes(0, 0, 0);
    return date;
  },

  getTZDiff(timezone) {
    const now = moment();
    const localOffset = now.utcOffset();
    now.tz(timezone);
    const centralOffset = now.utcOffset();
    return (localOffset - centralOffset) / 60;
  },

  convert_timezone(date, tzString) {
    return new Date((typeof date === "string" ? new Date(date) : date).toLocaleString("en-US", { timeZone: tzString }));
  },

  params(params, content) {
    if (params) {
      let obj = null;

      if (typeof params === "string") {
        obj = Common.toLowerKeys(JSON.parse(params));
      } else if (typeof params === "object") {
        obj = Common.toLowerKeys(params);
      }

      if (obj) {
        const pattern = /\%(.*?)\%/;
        let match;
        let count = 0;
        while ((match = content.match(pattern))) {
          const find = match[0].slice(1, -1).toLowerCase();
          if (obj[find]) content = content.replace(match[0], obj[find]);
          if (++count >= 100) break;
        }
      }
    }
    return content;
  },

  toLowerKeys(obj) {
    return Object.keys(obj).reduce((acc, key) => {
      acc[key.toLowerCase()] = obj[key];
      return acc;
    }, {});
  },

  get_url_extension(url) {
    return url.split(/[#?]/)[0].split(".").pop().trim();
  },

  ext2mime(url) {
    const mime = Common.get_url_extension(url);
    const types = {
      jpg: "image/jpeg",
      jpeg: "image/jpeg",
      png: "image/png",
      gif: "image/gif",
      webp: "image/webp",
      mp4: "video/mp4",
      mp3: "audio/mpeg",
      ogg: "audio/ogg",
      pdf: "application/pdf",
    };
    return types[mime];
  },

  get_file_name(url) {
    const filename = url.substring(url.lastIndexOf("/") + 1);
    return decodeURI(filename);
  },

  post_type(mime, type) {
    let post_type = "documentMessage";
    if (type === 1) {
      if (["image/png", "image/jpeg", "image/jpg", "image/gif"].includes(mime)) post_type = "imageMessage";
      else if (["video/mp4", "video/3gpp", "video/gif"].includes(mime)) post_type = "videoMessage";
      else if (["audio/mpeg", "audio/ogg"].includes(mime)) post_type = "audioMessage";
    } else {
      if (["png", "jpeg", "jpg", "gif"].includes(mime)) post_type = "imageMessage";
      else if (["mp4", "3gpp"].includes(mime)) post_type = "videoMessage";
      else if (["mp3", "ogg"].includes(mime)) post_type = "audioMessage";
    }
    return post_type;
  },

  response(res, row) {
    if (res && res.length > 0) return row || row === undefined ? res[0] : res;
    return false;
  },
};

export default Common;
