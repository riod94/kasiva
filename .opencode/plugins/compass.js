// compass:managed-file sha256:f5c52d416cc3ab7f5a3c0cc7ad4dba7e09c8563513c3a9e7d581058075694412
// compass agent reminder plugin
import { existsSync } from "fs";
import { join } from "path";

export const CompassPlugin = async ({ directory }) => {
  let reminded = false;
  return {
    "tool.execute.before": async (input, output) => {
      if (reminded) return;
      if (!existsSync(join(directory, "compass-out", "graph.json"))) return;
      if (input.tool === "bash") {
        output.args.command =
          'echo "[compass] Focused task: query first. Broad first session: read only Agent Orientation at the start of GRAPH_REPORT.md, then query. Inspect direction, ambiguity, completeness, domain truncation, pagination, and minimal cited source. Keep compass watch running or update after edits." ; ' +
          output.args.command;
        reminded = true;
      }
    },
  };
};
