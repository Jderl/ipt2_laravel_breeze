# Classroom presenter notes

Allow approximately 20–30 minutes. Use synthetic records. Keep VS Code, the Artisan terminal, a browser, and phpMyAdmin ready. The lesson PDF was not supplied; these scripts follow the requested workflow.

## Short scripts for the major steps

| Step | Open or run | Say |
| --- | --- | --- |
| Setup | .env; php artisan --version | “Laravel provides our structure. These settings tell it where our database server is. We are using version 10.” |
| Database | migrations; php artisan migrate:status | “A migration is a versioned recipe for a table. Everyone running it gets the same structure. The courses table comes first because students refer to it.” |
| MVC | Model, controller, and Blade side by side | “The model works with records, the view presents them, and the controller connects a request to the right work. Each has a different responsibility.” |
| Eloquent | Student.php | “ORM means Object-Relational Mapper. We call PHP methods like create and update; Eloquent translates them into database operations.” |
| Relationships | Course::students and Student::course | “One course can have many students. Each student belongs to one course. course_id connects them; it stores an ID rather than repeating the course name.” |
| Seeders | DatabaseSeeder; php artisan db:seed | “Seeders give us predictable sample records. We add courses first so every student's course exists.” |
| Routes | routes/web.php; route:list --path=students | “This one resource declaration registers seven actions. The URL and request method together decide which action runs.” |
| Controller | StudentController.php | “Index reads a page, create opens the form, store inserts, show reads one record, edit opens existing values, update saves them, and destroy deletes.” |
| Blade/Bootstrap | _form.blade.php; layouts/app.blade.php | “Blade turns our data into HTML. Bootstrap supplies consistent buttons, forms, spacing, and a table that can scroll on smaller screens.” |
| Validation | validatedData() | “We check all fields on the server before saving. An email must look like an email, and numbers and emails cannot belong to two students.” |
| CSRF/methods | @csrf and @method | “The token connects the form to this session. Hidden method fields let a normal HTML form request update or delete.” |
| Complete flow | PROCESS-FLOW.md | “Follow the request: browser, route, controller, model, database, then the response. A write redirects to a fresh read.” |

## Exact browser demonstration sequence

1. Run `php artisan migrate --seed`, then `php artisan serve --host=127.0.0.1 --port=8000`. Open **http://127.0.0.1:8000/students**. Point out the 12 records, course labels, and actions. Explain index → Eloquent → MySQL → Blade.
2. Click **2** in pagination. Show the remaining two records, then click **1**. Explain that the browser requests another page of data; no rows are changed.
3. Click **Add student**. Explain that GET create loads course choices and displays a form; it has not inserted anything.
4. Fill **Student number: DEMO-2026-001**, **First name: Maria**, **Last name: Dela Cruz**, **Email: maria.demo@example.com**, **Year level: Year 2**, **Course: BSIT**.
5. Click **Save student**. Observe success and a total of 13. Explain POST store → validation → Student::create → INSERT → redirect → GET index. Refresh and confirm the count remains 13, showing the form was not resubmitted.
6. Maria sorts by last name, so find **Dela Cruz, Maria** on page 1 and click its **View** button. Explain route binding, show(), and $student->course. In phpMyAdmin browse students and find DEMO-2026-001; compare course_id with the BSIT row in courses.
7. Click **Edit student**, change **Year level to Year 3** and **Course to BSCS**, leave email and number unchanged, and click **Save changes**. Show success, Year 3, and BSCS. Explain hidden PUT, unique-ignore-current-model, UPDATE, and the redirect to details.
8. Refresh details. Confirm changes remain. Return to the directory and verify the course there too.
9. Click **Add student** again. Enter **DEMO-2026-001**, **Test**, **Student**, **maria.demo@example.com**, **Year 1**, **BSIT**. Submit. Observe both duplicate errors and retained values. Explain that validation stopped a second insert; the database also has unique constraints.
10. Correct the number to **DEMO-2026-002** and email to **test.student@example.com**, then click **Cancel**. Confirm no extra record was created. To demonstrate required/invalid email feedback, open Add student and submit empty fields or an invalid email; browser validation appears first. Server-validation checks are documented separately in TESTING.md.
11. Return to Maria's details and click **Delete**. First choose **Cancel** and show the profile remains. Click Delete again and choose **OK**. Observe success and total back to 12. Explain DELETE → destroy → Eloquent → MySQL → redirect → GET list.
12. Refresh and confirm Maria is gone. Visit her former details URL from browser history and show 404. In phpMyAdmin verify her row is gone and the BSCS course still exists.

If the demonstration is interrupted after creation, search visually for DEMO-2026-001 and resume editing/deleting it. Do not repeatedly seed/reset to hide what happened; show students that data persists independently of the page.

## Questions to ask learners

- Which method opens the blank form, and which method actually saves it?
- Why does a student store course_id instead of a whole course name?
- Why must an update permit the student's existing email?
- What does the browser receive: PHP objects or rendered HTML?
- Why do we validate on the server even when the form says required?

End with the recap in PROCESS-FLOW.md and the final checklist in TESTING.md.
