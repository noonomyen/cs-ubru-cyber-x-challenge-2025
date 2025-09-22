import { DatabaseSync } from "node:sqlite"
import { fileURLToPath } from "node:url"
import path from "node:path"
import express from "express"

if (!process.env.GZCTF_FLAG) {
    console.error("GZCTF_FLAG environment variable is not defined.")
    process.exit(1)
}

const __dirname = path.dirname(fileURLToPath(import.meta.url))

const db = new DatabaseSync(":memory:")

const secret = Math.random().toString(36).slice(2)

console.log(`Password generated: ${secret}`)

db.exec(`
CREATE TABLE users (username, password);
INSERT INTO users VALUES ('admin', '${secret}')
`)

const app = express()

app.use(express.static(path.join(__dirname, "public")))
app.use(express.json())

app.post("/api/login", (req, res) => {
    const { username, password } = req.body || {}
    if (typeof username !== "string" || typeof password !== "string") {
        return res.json({ status: "fail" })
    }
    try {
        const query = `SELECT * FROM users WHERE username = '${username}' AND password = '${password}'`
        const row = db.prepare(query).get()
        if (row) {
            return res.json({ status: "ok", message: `Welcome! Your flag is: ${process.env.GZCTF_FLAG}` })
        } else {
            return res.json({ status: "fail" })
        }
    } catch {
        return res.json({ status: "fail" })
    }
})

app.listen(8080, "0.0.0.0", () => {
    console.log("Started, listening at port 8080")
})
