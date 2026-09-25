ASTANA DEZ CONTROL — GitHub + Render

1. Upload all files from this archive to the ROOT of a GitHub repository.
2. In Render create a Web Service from that repository. Runtime: Docker.
3. In Render > Environment set only:
   ADMIN_USERNAME = your login
   ADMIN_PASSWORD = your password
4. Deploy. Admin panel: https://YOUR-SERVICE.onrender.com/admin/

The admin login form asks only for Login and Password. No hash is required.
The site uses SQLite for content and leads. On Render Free, the local filesystem is ephemeral: admin edits/uploads can reset after a redeploy/restart. For permanent client use, attach a persistent disk or external persistent database.
