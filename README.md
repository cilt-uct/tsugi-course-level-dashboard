# tsugi-course-level-dashoard

## Configuration
1. Copy `cp config-dist.cfg config.cfg`
2. Update the details of `config.cfg` to connect to the REST API

```
real_weeks = false    [Default: false - Use actual week numbers, not counting from 1 upwards]
```

#### Enable Downloads for project / course site without provider information

Add the following to custom properties for the LTI tool:
```
download=enable/disabled [Default: disabled - force showing download button]
real_week_no=true/false  [Default: false - Use actual week numbers, not counting from 1 upwards]
```
