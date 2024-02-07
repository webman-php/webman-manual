# Packaging

For example, to package the foo application plugin:

* Set the version number in `plugin/foo/config/app.php` (**important**).
* Delete any unnecessary files in the `plugin/foo` directory, especially temporary files related to uploading functions in `plugin/foo/public`.
* Remove database and Redis configurations. If your project has its own separate database and Redis configurations, these configurations should be triggered in the installation bootstrap program when the application is accessed for the first time (you need to implement this yourself) and let the administrator manually fill them in and generate them.
* Restore any other files that need to be restored to their original state.
* After completing the above steps, navigate to the `{main project}/plugin/` directory and use the command `zip -r foo.zip foo` to generate `foo.zip`.
