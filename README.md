The goal of this project is to make it easy to track metrics.

## Configuration

Just create a `.k-pi.dist.yaml` file following the following pattern:

```yaml
---
reports:
  <report-name>:
    storage:
      <storage-type>: <storage-config>
```

## Integration with CI

### Github Actions

## Some examples

For all examples, please refer to the configuration file
[`.k-pi.dist.yaml`](./.k-pi.dist.yaml).

### Track test coverage

> See [`example-coverage.yaml`](./.github/workflows/example-coverage.yaml)

### Track Github dependabot security alerts

> See
> [`example-github-security.yaml`](./.github/workflows/example-github-security.yaml)

The goal of this metric is to count the number of alerts from dependabot's
Github API and project them onto a graph to track their evolution over time.

In the configuration file, you can see that the configured storage is
`github-discussion`. It means that data will be stored in the discussion but
also projected onto a graph.

### Track PhpMetrics data

> See [`example-phpmetrics.yaml`](./.github/workflows/example-phpmetrics.yaml)
