# AI Log

## Milestone 1

### Tools and tasks
- **Claude (claude.ai chat, file creation)** – generated the Milestone 1 project skeleton: folder structure, `.gitignore`, `config.example.php`, the static SPA (`index.html`, CSS, `app.js`, `router.js`, nine views), the README draft and the agent/skill files.
  Main prompt: "I need you to follow the requirements for milestone 1 and make me a design based on SB Admin 2. Hook everything up to work for milestone 1. Output a finished .zip file clearly named. Make no mistakes and good luck."
  Input I supplied: Project Requirements (building_managment_system), my `AGENTS.md` and my draft ERD (`docs/ERD.png`).
  
- **Student (after code review)** - I kept most of what Claude did in terms of design. Only changed the colors nothing major. Later during the testing of the app I found 2 routing mistakes, which I assume emerged from duplicate files (explained below), I fixed them very easily. Nothing was "rejected" only fixed.  

### Where the AI was wrong
<!-- At least 2 real cases are required. Write only what actually happened. -->
1. There was a routing issue when going from the `../dashboard.html` to the `../users.html` view. The route was pointed to a non existing `../user.html` file instead of the correct `../users.html`. Fixed manually. 
2. Same exact problem happened from `../dashboard.html` to the `../announcments.html` view. Also fixed manually when I noticed.
3. The forms in `../profile.html` were set up as "type = text". Changed them to password. This was my oversight from the "Project Requirements" document I sent over, where I didn't specify.



## Milestone 2

### Tools and tasks 
- **Claude (claude.ai chat)** - generated the database data, alerted the user about a missing password field.
  Main prompt: "Can you fill in the tables with some dummy data that will be consistent with the frontend data, including my credentials for the admin login, from milestone1, and also check if we are missing any important parts of the database. Update the agents.md and the AI_logs with appropriate information, specify the rules for the database in agents.md."
  Input I supplied: building_managment.sql.

- **Student** - I had already made the database prior to working on the dao part, so I had claude fill it with dummy data while I started on the dao layer. Once he was finished I added the "password_hash" field into the database, that will only keep the hashed version of the password which will be implemented later on using password_hash and password_verify.

### Where the AI was wrong
1. It filled the dummy data according to the template I was using, and not my credentials even tho it was stated in the request, easily fixed.
2. Forgot to update the AI_LOG.md with the prompts.
3. Forgot to update the agents.md file with the rules for the database that were given in the request.