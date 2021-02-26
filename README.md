# tsugi-course-level-dashoard

## Configuration
1. Copy `cp tool-config-dist.php tool-config.php`
2. Update the details of `tool-config.php` to connect to the REST API

Set `real_weeks` to `true` to count from 1 upwards.
[ Default: false - Use actual week numbers, not counting from 1 upwards ]

#### Enable Downloads for project / course site without provider information

Add the following to custom properties for the LTI tool:
```
download=enable/disabled [Default: disabled - force showing download button]
real_week_no=true/false  [Default: false - Use actual week numbers, not counting from 1 upwards]
```
